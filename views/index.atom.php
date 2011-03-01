<feed xmlns="http://www.w3.org/2005/Atom" xmlns:ld="http://ladistribution.net/#ns" xmlns:activity="http://activitystrea.ms/spec/1.0/">
<id></id>
<title><?php echo out( $application->getName() ) ?></title>
<link rel="self" type="application/atom+xml" href="<?php echo $application->getUrl() ?>feed"/>
<updated><?php echo date("c") ?></updated>
<?php foreach ($entries as $entry) : ?>
<entry>
  <id></id>
  <title><?php out( $entry['title'] ) ?></title>
  <content type="html"><![CDATA[<?php echo $entry['content'] ?>]]></content>
<?php if (!empty($entry['user'])) : ?>
  <ld:username><?php out($entry['user']['username']) ?></ld:username>
<?php endif ?>
  <author>
    <name><?php out( $entry['screenName'] ) ?></name>
    <uri><?php out( $entry['userUrl'] ) ?></uri>
  </author>
  <ld:type>link</ld:type>
  <ld:action>posted a link</ld:action>
  <published><?php echo date("c", $entry['timestamp']) ?></published>
  <link href="<?php out( $entry['link'] ) ?>"/>
  <link rel="related" href="<?php out( $entry['link'] ) ?>"/>
  <link rel="self" type="application/atom+xml" href=""/>
  <link rel="avatar" href="<?php out( $entry['avatarUrl'] ) ?>"/>
<?php if (!empty($entry['screenshot'])) : ?>
  <link rel="enclosure" href="<?php out( $entry['screenshot'] ) ?>"/>
<?php endif ?>
  <activity:verb>http://activitystrea.ms/schema/1.0/post</activity:verb>
  <activity:object>
    <activity:object-type>http://activitystrea.ms/schema/1.0/bookmark</activity:object-type>
  </activity:object>
</entry>
<?php endforeach ?>
</feed>