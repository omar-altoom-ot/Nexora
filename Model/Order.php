

<?php

// <?php

// class Order
// {
//	public ?int $id = null;
//	public int $customer_id = 0;
//	public ?string $order_date = null;
//	public string $total_amount = '0.00';
//	public string $status = 'pending';
//	public string $shipping_address = '';
// }

class Order
{
	public int $id;
	public int $customer_id;
	public string $order_date;
	public float $total_amount;
	public string $status;
	public string $shipping_address;

	// Optional: These are joined from the customers table for the admin view
	public ?string $first_name = null;
	public ?string $last_name = null;
	public ?string $email = null;
	public ?string $phone = null;
	public ?int $items_count = null;

	// Map array data to object properties
	public function __construct(array $data = [])
	{
		if (!empty($data)) {
			$this->id = (int)($data['id'] ?? 0);
			$this->customer_id = (int)($data['customer_id'] ?? 0);
			$this->order_date = $data['order_date'] ?? '';
			$this->total_amount = (float)($data['total_amount'] ?? 0);
			$this->status = $data['status'] ?? '';
			$this->shipping_address = $data['shipping_address'] ?? '';
			
			$this->first_name = $data['first_name'] ?? null;
			$this->last_name = $data['last_name'] ?? null;
			$this->email = $data['email'] ?? null;
			$this->phone = $data['phone'] ?? null;
			$this->items_count = isset($data['items_count']) ? (int)$data['items_count'] : null;
		}
	}
}