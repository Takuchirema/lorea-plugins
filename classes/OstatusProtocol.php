<?php
class OstatusProtocol {
	static function getFeed($webid) {
		$endpoint = SalmonDiscovery::getYadisEndpoint($webid,
                        "//xrd:Link[attribute::type='application/atom+xml']/@href", "application/atom+xml");
		$request = curl_init($endpoint);
		curl_setopt($request, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);

		$data = curl_exec($request);
		$ret_code = curl_getinfo($request, CURLINFO_HTTP_CODE);
		if ($ret_code == 200 || $ret_code == 302) {
			$notification = new FederatedNotification();
			$xml = @ new SimpleXMLElement($data);
			$notification->load($xml, false);
			return $notification;
		}
	}
}
