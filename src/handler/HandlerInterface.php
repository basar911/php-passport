<?php

namespace basar911\phpPassport\handler;

interface HandlerInterface
{
    public function encrypt($data, $key);
    public function decrypt($data, $key);
}