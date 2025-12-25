<?php

function test_that_true_is_true() {
    if (true !== true) {
        throw new \Exception('assertTrue failed: expected true, got ' . var_export(true, true));
    }
}