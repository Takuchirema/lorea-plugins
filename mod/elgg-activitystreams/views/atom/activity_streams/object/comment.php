<?php

$comment = $vars['annotation'];

?>

<id><?php echo ActivityStreams::getAnnotationAtomID($comment); ?></id>
<content><![CDATA[<?php 
	echo elgg_view('output/longtext', array('value' => $comment->value)); 
?>]]></content>
<published>
	<?php echo date(DATE_ATOM, $comment->time_created); ?>
</published>
<link rel="alternate" type="text/html" href="<?php echo htmlspecialchars($comment->getURL()); ?>" />
<activity:object-type>http://activitystrea.ms/schema/1.0/comment</activity:object-type>
