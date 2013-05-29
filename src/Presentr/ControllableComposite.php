<?php
namespace Presentr;

class ControllableComposite implements Controllable {
    protected $_controllables;

    public function __construct() {
        $this->_controllables = new \SplObjectStorage;
    }

    public function add(Controllable $item) {
        $this->_controllables->attach($item);

        return $this;
    }

    public function command($directive) {
        foreach ($this->_controllables as $controllable) {
            $controllable->command($directive);
        }
    }

    public function enableRemote() {
        foreach ($this->_controllables as $controllable) {
            $controllable->enableRemote();
        }
    }

    public function disableRemote() {
        foreach ($this->_controllables as $controllable) {
            $controllable->disableRemote();
        }
    }
}