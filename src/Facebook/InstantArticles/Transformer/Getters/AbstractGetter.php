<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Getters;

use Facebook\InstantArticles\Validators\Type;
use Symfony\Component\CssSelector\CssSelectorConverter;

/**
 * Class abstract for all getters.
 */
abstract class AbstractGetter
{
    /**
     * Method that should be implemented so it can be Instantiated by @see GetterFactory
     * @param array(string-> string) $configuration With all properties of this Getter
     * @return $this Returns the self instance configurated.
     */
    abstract public function createFrom($configuration);

    /**
     * Method that should retrieve
     */
    abstract public function get($node);
}
