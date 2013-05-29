<?php
namespace Presentr;

interface Controllable {
    public function command($directive);
    public function enableRemote();
    public function disableRemote();
}