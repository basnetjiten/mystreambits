<?php

namespace App;

use Illuminate\Support\Facades\Request;

class PayoutSettings
{
    public static function validate_list(Request $request) {
        $request->validate([
            'username' => 'required',
            'password' => 'required|min:8|max:255',
        ]);
    }

}