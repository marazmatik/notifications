<?php

/*
 * This file is part of SeAT
 *
 * Copyright (C) 2015, 2016, 2017, 2018  Leon Jacobs
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace Seat\Notifications\Http\Validation;

use Illuminate\Foundation\Http\FormRequest;
use Seat\Eveapi\Models\Account\ApiKeyInfoCharacters;
use Seat\Eveapi\Models\Corporation\CorporationSheet;

/**
 * Class GroupAffiliation.
 * @package Seat\Notifications\Http\Validation
 */
class GroupAffiliation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        // Instead of using the 'exists' validation rule, we opt to use
        // the 'in' rule. We do this because we want to add '0' as a valid
        // value, which will signal a wild card for either all characters
        // or all corporations.
        $character_ids = implode(',',
            array_prepend(ApiKeyInfoCharacters::pluck('characterID')->toArray(), 0));
        $corporation_ids = implode(',',
            array_prepend(CorporationSheet::pluck('corporationID')->toArray(), 0));

        return [
            'id'             => 'required|numeric|exists:notification_groups,id',
            'characters'     => 'required_without_all:corporations',
            'corporations'   => 'required_without_all:characters',
            'characters.*'   => 'in:' . $character_ids,
            'corporations.*' => 'in:' . $corporation_ids,
        ];

    }
}
