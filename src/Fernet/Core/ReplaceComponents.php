<?php

declare(strict_types=1);

namespace Fernet\Core;

use Fernet\Params;

class ReplaceComponents
{
    const REGEX_TAG_WITH_CHILD = '/<([A-Z][\w0-9_\-]+)([^>]*)>(.+?)<\/\1>/s';
    const REGEX_TAG = '/<([A-Z][\w0-9_\-]+)([^>]*)\/>/s';
    const REGEX_ATTRIBUTE = '/(\w+)=(["\'])(.+)\2/s';
    const REGEX_ATTRIBUTE_WITH_OBJECT = '/(\w+)={(.+)}/s';

    public function replace(string $content): string
    {
        $raws = [];
        $contents = [];
        // TODO prevent circular reference between components
        // TODO throw error if there're components inside child content
        foreach ([static::REGEX_TAG, static::REGEX_TAG_WITH_CHILD] as $regexp) {
            if (preg_match_all($regexp, $content, $matches)) {
                foreach ($matches[1] as $i => $tag) {
                    $raws[] = $matches[0][$i];
                    $params = $this->parseAttributes($matches[2][$i]);
                    $childContent = $matches[3][$i] ?? '';
                    $contents[] = (new ComponentElement($tag, $params, $childContent))->render();
                }
            }
        }

        return str_replace($raws, $contents, $content);
    }

    public function parseAttributes(string $raw): array
    {
        $attributes = [];
        if (preg_match_all(static::REGEX_ATTRIBUTE, $raw, $matches)) {
            foreach ($matches[1] as $i => $key) {
                $attributes[$key] = $matches[3][$i];
            }
        }
        if (preg_match_all(static::REGEX_ATTRIBUTE_WITH_OBJECT, $raw, $matches)) {
            foreach ($matches[1] as $i => $key) {
                $attributes[$key] = Params::get($matches[2][$i]);
            }
        }

        return $attributes;
    }
}
