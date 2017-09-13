<?php

class Test
{
    public function bla($test)
    {
        if ('a' == 'b') {
            return 'hallo';
        }

        try {
            echo 'test';
        } catch (\Exception $e) {
            echo 'error';
        }

        return 'test';
    }
}