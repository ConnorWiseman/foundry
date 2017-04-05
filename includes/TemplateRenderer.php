<?php

namespace Foundry;

require_once('Template.php');

final class TemplateRenderer {

    private $path, $layoutsDir, $partialsDir, $viewsDir;

    private function parseForEachBlocks($string, Array $context) {
        $forEachRegex = '/(?:{{foreach:)([a-zA-Z_-]*?)}}(?:\s)*?(.*?)?(?:\s)*?(?:{{\/foreach}})/s';
        preg_match_all($forEachRegex, $string, $blocks);
        list($matches, $items, $forEach) = $blocks;

        foreach($items as $itemIndex => $itemName) {

            if (!array_key_exists($itemName, $context)) {
                $string = str_replace($matches[$itemIndex], '', $string);
                continue;
            }

            $actualItems = $context[$itemName];
            if (is_null($actualItems)) {
                $string = str_replace($matches[$itemIndex], '', $string);
                continue;
            }
            $fragments = Array();

            foreach($actualItems as $currentItem) {

                $fragment = $forEach[$itemIndex];
                $subContext = Array();

                foreach($currentItem as $key => $value) {
                    $subContext["{$itemName}.{$key}"] = $value;
                }
                $fragment = $this->parseIfBlocks($fragment, $subContext);

                foreach ($currentItem as $key => $value) {
                    $key = "{{{$itemName}.{$key}}}";
                    $fragment = str_replace($key, $value, $fragment);
                }
                array_push($fragments, $fragment);
            }

            $result = implode("\r\n", $fragments);
            $string = str_replace($matches[$itemIndex], $result, $string);
        }
        return $string;
    }

    private function parseGravatars($string) {
        $gravatarRegex = '/(?:{{gravatar email=)(.*?)(?:\s)(?:size=)(.*?)(?:alt=)(.*?)(?:}})/';
        preg_match_all($gravatarRegex, $string, $gravatars);
        list($matches, $emails, $sizes, $alts) = $gravatars;
        foreach ($matches as $index => $match) {
            $email = $emails[$index];
            $size  = $sizes[$index];
            $alt   = $alts[$index];
            $img = "<img src=\"https://www.gravatar.com/avatar/${email}?s=${size}&r=g&d=mm\" width=\"${size}\" height=\"${size}\" alt=\"${alt}\" title=\"${alt}\">";
            $string = str_replace($match, $img, $string);
        }
        return $string;
    }

    private function parseIfBlocks($string, Array $context) {
        $ifRegex = '/(?:{{if:)([a-zA-Z_.-]*?)(?:}})(?:\s)*?([\s\S]*?)(?:(?:\s)*?{{else}}(?:\s)*?([\s\S]*?))?(?:\s)*?(?:{{\/if}})/';
        preg_match_all($ifRegex, $string, $blocks);
        list($matches, $conditions, $if, $else) = $blocks;

        foreach($conditions as $index => $condition) {
            if (array_key_exists($condition, $context) && $context[$condition]) {
                $string = str_replace($matches[$index], $if[$index], $string);
            } else if ($else[$index]) {
                $string = str_replace($matches[$index], $else[$index], $string);
            } else {
                $string = str_replace($matches[$index], '', $string);
            }
        }
        return $string;
    }

    private function parsePartials($string) {
        $partialRegex = '/(?:{{>)([a-zA-Z_\-\/]*?)(?:}})/';
        preg_match_all($partialRegex, $string, $partialMatches);
        list($partialPlaceholder, $partialFileNames) = $partialMatches;

        $partials = Array();
        foreach($partialFileNames as $index => $fileName) {
            $partial;
            if (array_key_exists($fileName, $partials)) {
                $partial = $partials[$fileName];
            }
            else {
                $partial = new Template(
                    $this->path,
                    $this->partialsDir,
                    $fileName
                );
                $partials[$fileName] = $partial;
            }
            $string = str_replace(
                $partialPlaceholder[$index],
                $partial->getContents(),
                $string
            );
        }
        return $string;
    }

    private function parseValues($string, Array $context) {
        foreach($context as $key => $value) {
            if (is_object($value) || is_array($value)) {
                continue;
            }
            $key = "{{{$key}}}";
            $string = str_replace($key, $value, $string);
        }

        return $string;
    }

    private function removeExtras($string) {
        return preg_replace('/({{(?:\>)?.*?}})/', '', $string);
    }

    public function __construct(Array $options) {
        $this->path          = $options['templatesPath'];
        $this->layoutsDir    = $options['layoutsDir'];
        $this->partialsDir   = $options['partialsDir'];
        $this->viewsDir      = $options['viewsDir'];
    }

    public function render($viewName, $context, $layoutName) {
        $layout = new Template($this->path, $this->layoutsDir, $layoutName);
        $view   = new Template($this->path, $this->viewsDir, $viewName);

        $layoutString = $layout->getContents();
        $viewString   = $view->getContents();

        $result = preg_replace('/{{{body}}}/', $viewString, $layoutString, 1);
        $result = $this->parsePartials($result);
        $result = $this->parseForEachBlocks($result, $context);
        $result = $this->parseIfBlocks($result, $context);
        $result = $this->parseValues($result, $context);
        $result = $this->parseGravatars($result);
        $result = $this->removeExtras($result);
        $result = preg_replace('/([{]{2,}(?:\>)?[a-z:]*?[}]{2,})/', '', $result);

        return $result;
    }

}
