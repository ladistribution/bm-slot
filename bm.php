<?php

class BmReader_Feed extends Ld_Feed_Merger_Feed
{

    public function getEntries()
    {
        $entries = parent::getEntries();
        foreach ($entries as $key => $value) {
            $entries[$key]['type'] = 'link';
            $entries[$key]['action'] = 'posted a link';
            if (isset($entries[$key]['enclosure'])) {
                $entries[$key]['screenshot'] = $entries[$key]['enclosure']->url;
            } else {
                $pu = parse_url($entries[$key]['link']);
                if (!empty($pu['host'])) {
                    $entries[$key]['screenshot'] = 'http://open.thumbshots.org/image.pxf?url=' . $pu['host'];
                }
            }
            $entries[$key]['tags'] = $entries[$key]['categories'];
        }
        return $entries;
    }

}

function getFeed($ressource = 'marks')
{
    global $application, $configuration;
    if (empty($configuration['maxItems'])) {
        $configuration['maxItems'] = 50;
    }
    $atomUrl = $configuration['bmUrl'] . '/' . $ressource;
    $atomUrl .= strpos($atomUrl, '?') === false ? '?format=atom' : '&format=atom';
    $atomUrl .= strpos($atomUrl, 'last=') === false ? '&last=' . $configuration['maxItems'] : '';
    $bmFeed = new BmReader_Feed($atomUrl, $application, 'public');
    return $bmFeed;
}

function getTags($ressource = 'tags')
{
    global $configuration, $cache;
    if ($cache) {
        $cacheKey = 'BmReader_Tags_' . md5($ressource);
        if ($cache->test($cacheKey)) {
            return $cache->load($cacheKey);
        }
    }
    $jsonUrl = $configuration['bmUrl'] . '/' . $ressource;
    $jsonUrl .= strpos($jsonUrl, '?') === false ? '?format=json' : '&format=json';
    $tags = Zend_Json::decode(Ld_Http::get($jsonUrl));
    $tags = prepareTags($tags);
    if ($cache) {
        $cache->save($tags, $cacheKey);
    }
    return $tags;
}

function prepareTags($tags)
{
    $maxPopularity = 1;
    foreach ($tags as $tag) {
        if ($tag['popularity'] > $maxPopularity) {
            $maxPopularity = $tag['popularity'];
        }
    }

    function prep($string)
    {
        $accents = "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
        $replacements = "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn";
        $string = strtr($string, $accents, $replacements);
        return strtolower($string);
    }

    function cmp($a, $b)
    {
        return strcmp(prep($a['label']), prep($b['label']));
    }

    usort($tags, "cmp");

    $pas = ($maxPopularity - 1) / 5;

    foreach ($tags as &$tag) {
        if ($tag['popularity'] <= $maxPopularity - 4 * $pas) {
            $tag['html'] = '<small><small>' . $tag['label'] . '</small></small>';
        } elseif ($tag['popularity'] <= $maxPopularity - 3 * $pas) {
            $tag['html'] = '<small>' . $tag['label'] . '</small>';
        } elseif ($tag['popularity'] <= $maxPopularity - 2 * $pas) {
            $tag['html'] = '<span>' . $tag['label'] . '</span>';
        } elseif ($tag['popularity'] <= $maxPopularity - 1 * $pas) {
            $tag['html'] = '<big>' . $tag['label'] . '</big>';
        } else {
            $tag['html'] = '<big><big>' . $tag['label'] . '</big></big>';
        }
    }

    return $tags;
}

function getTagsFromFeed($ressource = 'friends/marks', $maxItems = 100)
{
    global $cache;

    if ($cache) {
        $cacheKey = 'BmReader_Tags_' . md5($ressource);
        if ($cache->test($cacheKey)) {
            return $cache->load($cacheKey);
        }
    }

    $feed = getFeed($ressource);

    $tags = array();
    foreach ($feed->getEntries() as $entry) {
        foreach ($entry['tags'] as $tag) {
            $label = $tag['label'];
            if (empty($tags[$label])) {
                $tags[$label] = $tag;
                $tags[$label]['popularity'] = 1;
            } else {
                $tags[$label]['popularity'] ++;
            }
        }
    }

    // X most populars
    function cmp_tag_popularity($a, $b) { return $a['popularity'] < $b['popularity']; }
    usort($tags, "cmp_tag_popularity");
    $tags = array_slice($tags, 0, $maxItems);

    $tags = prepareTags($tags);

    if ($cache) {
        $cache->save($tags, $cacheKey, /* Tags */ array(), /* Lifetime */ 60 * 60 * 24);
    }

    return $tags;
}
