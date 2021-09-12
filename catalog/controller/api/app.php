<?php 
class ControllerApiApp extends Controller {
	public function index() {
		echo "Hello world";
	}

	public function getListProduct() {
		$json = array();

		$json['latests'] = array();
		$json['saleTops'] = array();

		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$limit = 10;

		$product_latests = $this->model_catalog_product->getLatestProducts($limit);


		if($product_latests) {
			foreach($product_latests as $product_latest) {
				if ($product_latest['image']) {
					$image = $this->model_tool_image->resize($product_latest['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price_ = $this->currency->format($this->tax->calculate($product_latest['price'], $product_latest['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price_ = false;
				}

				if (!is_null($product_latest['special']) && (float)$product_latest['special'] >= 0) {
					$special = $this->currency->format($this->tax->calculate($product_latest['special'], $product_latest['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$tax_price = (float)$product_latest['special'];
				} else {
					$special = false;
					$tax_price = (float)$product_latest['price'];
				}
	
				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format($tax_price, $this->session->data['currency']);
				} else {
					$tax = false;
				}

				$data['options'] = array();

				foreach ($this->model_catalog_product->getProductOptions($product_latest['product_id']) as $option) {
					$product_option_value_data = array();

					foreach ($option['product_option_value'] as $option_value) {
						if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
							if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
								$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
							} else {
								$price = false;
							}

							$product_option_value_data[] = array(
								'product_option_value_id' => $option_value['product_option_value_id'],
								'option_value_id'         => $option_value['option_value_id'],
								'name'                    => $option_value['name'],
								'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
								'price'                   => $price,
								'price_prefix'            => $option_value['price_prefix']
							);
						}
					}

					$data['options'][] = array(
						'product_option_id'    => $option['product_option_id'],
						'product_option_value' => $product_option_value_data,
						'option_id'            => $option['option_id'],
						'name'                 => $option['name'],
						'type'                 => $option['type'],
						'value'                => $option['value'],
						'required'             => $option['required']
					);
				}

				$json['latests'][] = array(
					'id' 		=> $product_latest['product_id'],
					'name' 		=> $product_latest['name'],
					'image' 	=> $image,
					'price'		=> $price_,
					'special'	=> $special,
			        'bgColor' 	=> $this->randomColor(),
			        'type' 		=> 'RUNNING',
			        'sizes'	=> $data['options'],
				);
			}
		}

		$saleTops = $this->model_catalog_product->getBestSellerProducts($limit);

		if($saleTops) {
			foreach ($saleTops as $key => $saleTop) {

				if ($saleTop['image']) {
					$image_sale_top = $this->model_tool_image->resize($saleTop['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				} else {
					$image_sale_top = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price_sale_top = $this->currency->format($this->tax->calculate($saleTop['price'], $saleTop['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price_sale_top = false;
				}

				if (!is_null($saleTop['special']) && (float)$saleTop['special'] >= 0) {
					$special_sale_top = $this->currency->format($this->tax->calculate($saleTop['special'], $saleTop['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$tax_price_sale_top = (float)$saleTop['special'];
				} else {
					$special_sale_top = false;
					$tax_price_sale_top = (float)$saleTop['price'];
				}
	
				if ($this->config->get('config_tax')) {
					$tax_sale_top = $this->currency->format($tax_price, $this->session->data['currency']);
				} else {
					$tax_sale_top = false;
				}

				$data['options'] = array();

				foreach ($this->model_catalog_product->getProductOptions($saleTop['product_id']) as $option) {
					$product_option_value_data = array();

					foreach ($option['product_option_value'] as $option_value) {
						if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
							if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
								$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
							} else {
								$price = false;
							}

							$product_option_value_data[] = array(
								'product_option_value_id' => $option_value['product_option_value_id'],
								'option_value_id'         => $option_value['option_value_id'],
								'name'                    => $option_value['name'],
								'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
								'price'                   => $price,
								'price_prefix'            => $option_value['price_prefix']
							);
						}
					}

					$data['options'][] = array(
						'product_option_id'    => $option['product_option_id'],
						'product_option_value' => $product_option_value_data,
						'option_id'            => $option['option_id'],
						'name'                 => $option['name'],
						'type'                 => $option['type'],
						'value'                => $option['value'],
						'required'             => $option['required']
					);
				}

				$json['saleTops'][] = array(
					'id' 		=> $saleTop['product_id'],
					'name' 		=> $saleTop['name'],
					'image' 	=> $image_sale_top,
					'price'		=> $price_sale_top,
					'special'	=> $special_sale_top,
			        'bgColor' 	=> $this->randomColor(),
			        'type' 		=> 'RUNNING',
			        'sizes'	=> $data['options'],
				);

				
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function randomColor() {
		$str = '#';

		for($i = 0; $i<6; $i++) {
			$randNum = rand(0, 15);

			switch ($randNum) {
				case '10':
					$randNum = 'A';
					break;
				case '11':
					$randNum = 'B';
					break;
				case '12':
					$randNum = 'C';
					break;
				case '13':
					$randNum = 'D';
					break;
				case '14':
					$randNum = 'E';
					break;
				case '15':
					$randNum = 'F';
					break;
			}

			$str .= $randNum;
		}

		return $str;
	}
}
