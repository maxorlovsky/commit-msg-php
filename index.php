<?php
class commitMsg {
    private $root;
    private $composer;
    private $message;

    public function __construct() {
        $this->composer = json_decode(file_get_contents(__DIR__ . '/../../../composer.json'));

        if (file_exists(__DIR__ . '/../../../.git/COMMIT_EDITMSG')) {
            $this->message = file_get_contents(__DIR__ . '/../../../.git/COMMIT_EDITMSG');
        }

        $this->config = [
            'types' => [
                'feat',
                'fix',
                'chore',
                'docs',
                'refactor',
                'style',
                'perf',
                'test',
                'revert',
            ],
            'lineLength' => 72,
            'scope' => [
                'mandatory' => false,
                'rules'     => '',
            ]
        ];

        return $this->init();
    }

    public function init() {
        if (isset($this->composer->config) && $this->composer->config) {
            $this->composer->config = (array)$this->composer->config;

            if (isset($this->composer->config['commit-msg']) && $this->composer->config['commit-msg']) {
                $this->populateConfig();
            }
        }

        // Breaking the message by line
        //$this->message = 'feat(CHZ-1): ';
        $this->message = explode("\n", $this->message);

        // Check if heading is in the message
        if (!$this->checkTypeSubject()) {
            $this->stop();
        }

        if (!$this->checkScope()) {
            $this->stop();
        }

        // Check if lines length is fine, check only if lineLength is integer and not boolean false
        if ($this->config['lineLength'] !== 0 && !$this->checkLength()) {
            $this->stop();
        }

        exit(0);
    }

    // Replace default config with config from package.json
    public function populateConfig() {
        $config = $this->composer->config['commit-msg'];

        // In case there is config.types available in package.json, check if it's array and if set, if set, replace default one
        if ($config->types && !is_array($config->types)) {
            echo 'commit-msg: config.types is suppose to be array';
            echo "\r\n";
            $this->stop();
        } else if ($config->types) {
            $this->config['types'] = $config->types;
        }

        // In case there is config.lineLength available in package.json, check if it's number and if set, if set, replace default one
        if ($config->lineLength && !is_numeric($config->lineLength)) {
            echo 'commit-msg: config.lineLength is suppose to be number, remove the rule or set to 0 if you don\'t want to force it';
            echo "\r\n";
            $this->stop();
        } else if ($config->lineLength) {
            $this->config['lineLength'] = $config->lineLength;
        }

        // In case there is config.scope, replace default one
        if ($config->scope) {
            $this->config['scope'] = $config->scope;
        }
    }

    public function checkTypeSubject() {
        $types = implode('|', $this->config['types']);
        $regStr = '(' . $types . ')(\(.*\))?\:';
        
        // Check type and semicolon
        if (!preg_match('/' . $regStr . '/i', $this->message[0])) {
            echo 'commit-msg: Type should follow the rules "' . $types . '(scope/filename): Subject"';
            echo "\r\n";
            return false;
        }

        $regStr .= ' .*';

        // Check if there is subject after semicolon
        if (!preg_match('/' . $regStr . '/i', $this->message[0])) {
            echo 'commit-msg: Subject is not set or there is no space after semicolon';
            echo "\r\n";
            return false;
        }

        return true;
    }

    // Check if scope after type is mandatory
    public function checkScope() {
        // Check first if config for scope is set up
        // If not, return true as default or old rules are set
        if (!$this->config['scope']) {
            return true;
        }

        // Check if rule for mandatory scope is set
        // If not, return true
        if (!$this->config['scope']->mandatory) {
            return true;
        }

        $rules = $this->config['scope']->rules;

        // Check if rules already have brackets
        // If yes, strip them
        if (substr($rules, 0, 1) === '(' && substr($rules, -1) === ')') {
            $rules = substr($rules, 1, -1);
        }

        // Add proper regex round brackets
        // Add additional backslashes for string to work properly with regex
        $regStr = '/\(' . $rules . '\)/i';

        // Check type and semicolon
        if (!preg_match($regStr, $this->message[0])) {
            echo 'commit-msg: Scope is not following the regex rules "' . $regStr .'"';
            echo "\r\n";
            echo 'commit-msg: Don\'t forget to put 2 backslashes instead of one';
            echo "\r\n";
            return false;
        }

        return true;
    }

    // Check length of every line of commit message and validate that there are no long lines in it
    public function checkLength() {
        // Start line with number one, to make it less confusing
        $line = 1;

        foreach ($this->message as $message) {
            // If line is longer than allowed, show error message and exit the process
            if (strlen($message) > $this->config['lineLength']) {
                echo 'commit-msg: Commit message in line '. $line .' have more characters than allowed in a single line, please break it down, maximum is '. $this->config['lineLength'];
                return false;
            }

            $line++;
        }

        return true;
    }

    public function stop() {
        exit(1);
    }
}

new \commitMsg;