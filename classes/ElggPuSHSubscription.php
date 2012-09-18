<?php
/***************************************************
 * Subscriber interfaces
 */
class ElggPuSHSubscription implements PuSHSubscriptionInterface {
        public $domain;
        public $subscriber_id;
        public $hub;
        public $topic;
        public $secret;
        public $status;
        public $post_fields;
        public $entity;
        // storage backend for subscriptions
        public function __construct($domain, $subscriber_id, $hub, $topic, $secret, $status = '', $post_fields = '') {
                $this->domain = $domain;
                $this->subscriber_id = $subscriber_id;
                $this->hub = $hub;
                $this->topic = $topic;
                $this->secret = $secret;
                $this->status = $status;
                $this->post_fields = $post_fields;
                $this->entity = null;
        }
        public function save() {
                if ($this->entity) {
                        $newObject = $this->entity;
                }
                else {
                        $newObject = new ElggObject();
                        $newObject->subtype = 'push_subscription';
                        $newObject->access_id = ACCESS_PUBLIC;
                        if (!$newObject->save())
                                error_log("foreign_objects: could not save");
                        $this->entity = $newObject;
                }
                $newObject->domain = $this->domain;
                $newObject->subscriber_id = $this->subscriber_id;
                $newObject->hub = $this->hub;
                $newObject->topic = $this->topic;
                $newObject->secret = $this->secret;
                $newObject->status = $this->status;
                $newObject->post_fields = serialize($this->post_fields);

        }

        public static function load($domain, $subscriber_id) {
                $metadata_pairs = array('domain' => $domain,
					'subscriber_id' => $subscriber_id);
                $entities = elgg_get_entities_from_metadata(array('metadata_name_value_pairs' => $metadata_pairs,
								  'types' => 'object',
								  'subtypes' => 'push_subscription'));
                if (!$entities) {
                        return null;
                }
                $s = $entities[0];
                $subscriber = new ElggPuSHSubscription($s->domain, $s->subscriber_id, $s->hub, $s->topic, $s->secret, $s->status, unserialize($s->post_fields));
                $subscriber->entity = $s;
                return $subscriber;
        }

        public function delete() {
                if ($subscriber->entity) {
                        $subscriber->entity->delete();
                        $subscriber->entity = null;
                }
        }
}


