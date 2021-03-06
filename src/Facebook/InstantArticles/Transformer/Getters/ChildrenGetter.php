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

class ChildrenGetter extends AbstractGetter
{
    protected $selector;

    public function createFrom($properties)
    {
        return $this->withSelector($properties['selector']);
    }

    public function findAll($node)
    {
        $domXPath = new \DOMXPath($node->ownerDocument);
        $converter = new CssSelectorConverter();
        $xpath = $converter->toXPath($this->selector);
        return $domXPath->query($xpath, $node);
    }

    public function withSelector($selector)
    {
        Type::enforce($selector, Type::STRING);
        $this->selector = $selector;

        return $this;
    }

    public function get($node)
    {
        $elements = self::findAll($node, $this->selector);
        if (!empty($elements)) {
            return $elements->item(0)->getChildren();
        }
        return null;
    }
}
