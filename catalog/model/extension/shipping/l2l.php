<?php
class ModelExtensionShippingL2l extends Model {
	function getQuote($address) {
		$this->load->language('extension/shipping/l2l');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('shipping_l2l_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if (!$this->config->get('shipping_l2l_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$shippingCost = 0;
			$subTotal = $this->cart->getSubTotal();
			$shippingCost = ($subTotal * 0.05) * 1.15;
			
			switch ($shippingCost){
			case $shippingCost < 180;
				$shippingCost = 180 * 1.15;
				break;
			case $shippingCost > 700;
				$shippingCost = 700 * 1.15;
				break;
			default:
				break;
			}


			$quote_data = array();

			$quote_data['l2l'] = array(
				'code'         => 'l2l.l2l',
				'title'        => $this->language->get('text_description'),
				'cost'         => $shippingCost,
				'tax_class_id' => $this->config->get('shipping_l2l_tax_class_id'),
				'text'         => $this->currency->format($this->tax->calculate($shippingCost, $this->config->get('shipping_flat_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
			);

			$method_data = array(
				'code'       => 'l2l',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('shipping_l2l_sort_order'),
				'error'      => false
			);
		}

		return $method_data;
	}
}