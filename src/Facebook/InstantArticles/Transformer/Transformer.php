<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer;

use Facebook\InstantArticles\Transformer\Warnings\UnrecognizedElement;

class Transformer
{
    private $rules = array();
    private $warnings = array();

    public function getWarnings()
    {
        return $this->warnings;
    }

    public function addRule($rule)
    {
        array_unshift($this->rules, $rule);
    }

    public function transform($context, $node)
    {
        $log = \Logger::getLogger('facebook-instantarticles-transformer');
        if (!$node) {
            $e = new \Exception();
            $log->error(
                'Transformer::transform($context, $node) requires $node'.
                ' to be a valid one. Check on the stacktrace if this is '.
                'some nested transform operation and fix the selector.',
                $e->getTraceAsString()
            );
        }
        $current_context = $context;
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                $matched = false;
                $log->debug("===========================");
                foreach ($this->rules as $rule) {
                    if ($rule->matches($context, $child)) {
                        $current_context = $rule->apply($this, $current_context, $child);
                        $matched = true;
                        break;
                    }
                }
                if (!$matched &&
                    !($child->nodeName === '#text' && empty(trim($child->textContent))) &&
                    !($child->nodeName === '#comment')
                    ) {
                    $tag_content = $child->ownerDocument->saveXML($child);
                    $tag_trimmed = trim($tag_content);
                    if (!empty($tag_trimmed)) {
                        $log->debug('context class: '.get_class($context));
                        $log->debug('node name: '.$child->nodeName);
                        $log->debug("CONTENT NOT MATCHED: \n".$tag_content);
                    } else {
                        $log->debug('empty content ignored');
                    }

                    $this->warnings[] = new UnrecognizedElement($current_context, $child);
                }
            }
        }
        return $context;
    }

    public function loadRules($json_file)
    {
        $configuration = json_decode($json_file, true);
        if ($configuration && isset($configuration['rules'])) {
            foreach ($configuration['rules'] as $configuration_rule) {
                $clazz = $configuration_rule['class'];
                try {
                    $factory_method = new \ReflectionMethod($clazz, 'createFrom');
                } catch (\ReflectionException $e) {
                    $factory_method =
                        new \ReflectionMethod(
                            'Facebook\\InstantArticles\\Transformer\\Rules\\'.$clazz,
                            'createFrom'
                        );
                }
                $this->addRule($factory_method->invoke(null, $configuration_rule));
            }
        }
    }
}
