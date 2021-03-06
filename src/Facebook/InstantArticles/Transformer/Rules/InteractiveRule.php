<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Interactive;
use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\InstantArticle;

class InteractiveRule extends ConfigurationSelectorRule
{
    const PROPERTY_IFRAME = 'interactive.iframe';
    const PROPERTY_URL = 'interactive.url';
    const PROPERTY_WIDTH_NO_MARGIN = Interactive::NO_MARGIN;
    const PROPERTY_WIDTH_COLUMN_WIDTH = Interactive::COLUMN_WIDTH;
    const PROPERTY_HEIGHT = 'interactive.height';
    const PROPERTY_CAPTION = 'interactive.caption';

    public function getContextClass()
    {
        return InstantArticle::class;
    }

    public static function create()
    {
        return new InteractiveRule();
    }

    public static function createFrom($configuration)
    {
        $interactive_rule = self::create();
        $interactive_rule->withSelector($configuration['selector']);

        $interactive_rule->withProperties(
            array(
                self::PROPERTY_IFRAME,
                self::PROPERTY_URL,
                self::PROPERTY_WIDTH_NO_MARGIN,
                self::PROPERTY_WIDTH_COLUMN_WIDTH,
                self::PROPERTY_HEIGHT,
                self::PROPERTY_CAPTION
            ),
            $configuration
        );

        return $interactive_rule;
    }

    public function apply($transformer, $instant_article, $node)
    {
        $interactive = Interactive::create();

        // Builds the image
        $iframe = $this->getProperty(self::PROPERTY_IFRAME, $node);
        $url = $this->getProperty(self::PROPERTY_URL, $node);
        if ($iframe) {
            $interactive->withHTML($iframe);
        }
        if ($url) {
            $interactive->withSource($url);
        }
        if ($iframe || $url) {
            $instant_article->addChild($interactive);
        } else {
            throw new \InvalidArgumentException('Invalid selector for '.self::PROPERTY_IFRAME);
        }

        if ($this->getProperty(self::PROPERTY_WIDTH_COLUMN_WIDTH, $node)) {
            $interactive->withWidth(Interactive::COLUMN_WIDTH);
        } else {
            $interactive->withWidth(Interactive::NO_MARGIN);
        }

        $height = $this->getProperty(self::PROPERTY_HEIGHT, $node);
        if ($height) {
            $interactive->withHeight($height);
        }

        $caption_node = $this->getProperty(self::PROPERTY_CAPTION, $node);
        if ($caption_node) {
            $transformer->transform($interactive, $node);
        }

        return $instant_article;
    }
}
