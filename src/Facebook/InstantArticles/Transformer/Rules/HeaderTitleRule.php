<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Transformer\Getters\GetterFactory;
use Facebook\InstantArticles\Transformer\Getters\StringGetter;
use Facebook\InstantArticles\Transformer\Getters\ChildrenGetter;

class HeaderTitleRule extends ConfigurationSelectorRule
{
    public function getContextClass()
    {
        return Header::class;
    }

    public static function create()
    {
        return new HeaderTitleRule();
    }

    public static function createFrom($configuration)
    {
        return self::create()->withSelector($configuration['selector']);
    }

    public function apply($transformer, $header, $h1)
    {
        $header->withTitle($h1->textContent);
        return $header;
    }
}
