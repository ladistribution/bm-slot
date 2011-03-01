<?php $baseUrl = isset($isFriends) ? url_for('friends/marks') : url_for('marks') ?>

<div class="h6e-page-content h6e-block<?php if ($hasMenu) echo ' has-tab' ?>">

<div class="ld-merger ld-feed">

<h3><?php echo $feed->getTitle() ?></h3>

<?php foreach ($feed->getEntries() as $entry) : ?>
    <div class="hentry">

        <?php printf('<img src="%1$s" width="%2$s" height="%2$s" alt="" class="avatar"/>', htmlspecialchars($entry['avatarUrl']), 32); ?>

        <div class="entry-inner">

            <?php if ($entry['screenName'] && $entry['userUrl']) : ?>
                <a class="username" href="<?php out($entry['userUrl']) ?>"><?php out($entry['screenName']) ?></a>
            <?php else : ?>
                <strong><?php out($entry['screenName']) ?></strong>
            <?php endif ?>

            <?php out($entry['action']) ?>

            <?php if (!empty($entry['screenshot'])) : ?>
                <a href="<?php out($entry['link']) ?>">
                    <img class="screenshot enclosure" src="<?php out($entry['screenshot']) ?>"/>
                </a>
            <?php endif ?>

            <h2 class="h6e-entry-title"><a href="<?php out($entry['link']) ?>"><?php out($entry['title']) ?></a></h2>

            <div class="h6e-post-content"><?php echo $entry['content'] ?></div>

            <div class="h6e-tags">
                <?php foreach ($entry['categories'] as $tag) : ?>
                    <a href="<?php out( $baseUrl . '/tag/' . urlencode($tag['label']) ) ?>" class="h6e-tag"><?php out($tag['label']) ?></a>
                <?php endforeach ?>
            </div>

            <div class="h6e-post-info"><?php echo Ld_Ui::relativeTime($entry['timestamp']) ?></div>

        </div>

    </div>
<?php endforeach ?>

</div>

</div>

<div class="h6e-sidebar h6e-block">

    <h3>Tags</h3>

    <div class="h6e-tag-list">
        <?php foreach ($tags as $tag) : ?>
            <a class="tag" href="<?php out($baseUrl . '/tag/' . urlencode($tag['label']) ) ?>"><?php echo $tag['html'] ?></a> &nbsp;
        <?php endforeach ?>
    </div>

</div>
