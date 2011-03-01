<?php

require_once dirname(__FILE__) . '/dist/prepend.php';

require_once 'limonade.php';

require_once 'bm.php';

function configure()
{
    global $site, $application;
    option('base_uri',      $site->getPath() . '/' . $application->getPath() );
    option('session',       false);
}

function before()
{
    global $site, $application, $configuration;
    set('site', $site);
    set('application', $application);
    set('configuration', $configuration);
    set('hasMenu', is_admin() && is_configured());
    layout('layout.html.php');
}

function is_configured()
{
    global $configuration;
    if (empty($configuration['bmUrl'])) {
        return false;
    }
    return true;
}

function not_configured()
{
    $settingsUrl = Ld_Ui::getApplicationSettingsUrl();
    return render(sprintf(
        '<div class="h6e-block">
            <h2>Not configured</h2>
            <p>You can configure this application in the <a href="%s">Settings</a>.</p>
        </div>', $settingsUrl));
}

function is_admin()
{
    global $site;
    $role = $site->getAdmin()->getUserRole();
    return $role == 'admin';
}

function not_admin()
{
    return redirect_to('marks');
}

function out($text)
{
    echo htmlspecialchars($text);
}

dispatch('/', 'index');

function index()
{
    if (is_admin()) {
        redirect_to('friends/marks');
    }
    return marks();
}

dispatch('/marks', 'marks');

function marks()
{
    if (!is_configured()) { return not_configured(); }
    set('feed', getFeed('marks'));
    set('tags', getTags('tags?last=75'));
    set('isMarks', true);
    return render("index.html.php");
}

dispatch('/marks/tag/:tag', 'marks_tag');

function marks_tag()
{
    if (!is_configured()) { return not_configured(); }
    $tag = urlencode(params('tag'));
    $feed = getFeed("marks/tag/$tag");
    set('feed', $feed);
    set('tags', getTags('tags?last=75'));
    set('isMarks', true);
    return render("index.html.php");
}

dispatch('/friends/marks', 'friends_marks');

function friends_marks()
{
    if (!is_admin()) { return not_admin(); }
    if (!is_configured()) { return not_configured(); }
    $feed = getFeed('friends/marks');
    set('feed', $feed);
    $tags = getTagsFromFeed('friends/marks?last=250', 75);
    set('tags', $tags);
    set('isFriends', true);
    return render("index.html.php");
}

dispatch('/friends/marks/tag/:tag', 'friends_marks_tag');

function friends_marks_tag()
{
    if (!is_admin()) { return not_admin(); }
    if (!is_configured()) { return not_configured(); }
    $tag = urlencode(params('tag'));
    $feed = getFeed("friends/marks/tag/$tag");
    set('feed', $feed);
    $tags = getTagsFromFeed('friends/marks?last=250', 75);
    set('tags', $tags);
    set('isFriends', true);
    return render("index.html.php");
}

dispatch('/feed', 'feed');

function feed()
{
    $feed = getFeed('marks');
    set('entries', $feed->getEntries());
    return xml("index.atom.php", null);
}

dispatch('/friends/feed', 'friends_feed');

function friends_feed()
{
    $feed = getFeed('friends/marks');
    set('entries', $feed->getEntries());
    return xml("index.atom.php", null);
}

run();
