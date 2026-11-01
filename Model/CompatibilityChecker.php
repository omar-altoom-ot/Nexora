<?php
// Model/CompatibilityChecker.php
require_once __DIR__ . '/../Config/DBconnection.php';

class CompatibilityChecker
{
    private Db $db;
    private const CATEGORIES = ['cpu'=>1,'gpu'=>2,'motherboard'=>3,'ram'=>4,'storage'=>5,'psu'=>6,'case'=>7,'cooler'=>10];

    public function __construct(?Db $db = null) { $this->db = $db ?? new Db(); }

    public function check(array $selection): array
    {
        $parts = [];
        foreach ($selection as $slot => $id) {
            if (!isset(self::CATEGORIES[$slot]) || !is_numeric($id) || (string)(int)$id !== (string)$id || (int)$id < 1) {
                throw new InvalidArgumentException('Invalid selected component.');
            }
            $row = $this->db->run_query(
                'SELECT p.id,p.name,p.category_id,p.is_active,p.stock_quantity,s.socket_type,s.ram_type,s.form_factor,s.power_watts,s.tdp_watts,s.gpu_length_mm,s.max_gpu_length_mm,s.cooler_height_mm,s.max_cooler_height_mm,s.cooler_sockets,s.supported_form_factors,s.storage_interface FROM products p LEFT JOIN product_specs s ON s.product_id=p.id WHERE p.id=?',
                [(int)$id]
            )->fetch(PDO::FETCH_ASSOC);
            if (!$row || (int)$row['category_id'] !== self::CATEGORIES[$slot] || !(int)$row['is_active']) {
                throw new InvalidArgumentException('Product does not match the selected component category.');
            }
            $parts[$slot] = $row;
        }
        $checks=[];
        $add = static function(string $name,string $status,string $message) use (&$checks): void {
            $checks[]=['title'=>$name,'status'=>$status,'message'=>$message];
        };
        $pair = static function(string $a,string $b,string $fieldA,string $fieldB,string $name) use ($parts,$add): void {
            if (!isset($parts[$a],$parts[$b])) return;
            $x=trim((string)($parts[$a][$fieldA]??''));$y=trim((string)($parts[$b][$fieldB]??''));
            if ($x==='' || $y==='') $add($name,'unknown','Missing specification; cannot verify.');
            elseif (strcasecmp($x,$y)===0) $add($name,'compatible',"Matching specification: {$x}.");
            else $add($name,'incompatible',"{$x} is not compatible with {$y}.");
        };
        $pair('cpu','motherboard','socket_type','socket_type','CPU / Motherboard socket');
        $pair('ram','motherboard','ram_type','ram_type','RAM / Motherboard type');
        if(isset($parts['motherboard'],$parts['case'])) {
            $factor=trim((string)($parts['motherboard']['form_factor']??''));
            $supported=trim((string)($parts['case']['supported_form_factors']??''));
            if($factor===''||$supported==='')$add('Motherboard / Case','unknown','Case support or motherboard form factor missing.');
            else {
                $fits=in_array(strtolower($factor),array_map(static fn($v)=>strtolower(trim($v)),explode(',',$supported)),true);
                $add('Motherboard / Case',$fits?'compatible':'incompatible',$fits?'Case supports this motherboard form factor.':"Case does not list {$factor} as supported.");
            }
        }
        if(isset($parts['gpu'],$parts['case'])) {
            $gpu=$parts['gpu']['gpu_length_mm']??null;$max=$parts['case']['max_gpu_length_mm']??null;
            if(!$gpu||!$max)$add('GPU / Case clearance','unknown','GPU length or case clearance missing.');
            else $add('GPU / Case clearance',(int)$gpu<=(int)$max?'compatible':'incompatible',"GPU {$gpu} mm; case limit {$max} mm.");
        }
        if(isset($parts['cooler'],$parts['case'])) {
            $height=$parts['cooler']['cooler_height_mm']??null;$max=$parts['case']['max_cooler_height_mm']??null;
            if(!$height||!$max)$add('Cooler / Case clearance','unknown','Cooler height or case clearance missing.');
            else $add('Cooler / Case clearance',(int)$height<=(int)$max?'compatible':'incompatible',"Cooler {$height} mm; case limit {$max} mm.");
        }
        if(isset($parts['cooler'],$parts['cpu'])) {
            $socket=trim((string)($parts['cpu']['socket_type']??''));$supported=trim((string)($parts['cooler']['cooler_sockets']??''));
            if(!$socket||!$supported)$add('Cooler / CPU socket','unknown','Cooler socket support not documented.');
            else {
                $fits=in_array(strtolower($socket),array_map(static fn($v)=>strtolower(trim($v)),explode(',',$supported)),true);
                $add('Cooler / CPU socket',$fits?'compatible':'incompatible',$fits?'Cooler supports the CPU socket.':"Cooler does not list {$socket} support.");
            }
        }
        if(isset($parts['psu'],$parts['cpu'],$parts['gpu'])) {
            $psu=$parts['psu']['power_watts']??null;$cpu=$parts['cpu']['tdp_watts']??null;$gpu=$parts['gpu']['tdp_watts']??null;
            if(!$psu||!$cpu||!$gpu)$add('PSU wattage (estimate)','unknown','CPU/GPU power data or PSU wattage missing.');
            else {
                $estimate=(int)ceil(((int)$cpu+(int)$gpu+100)*1.25);
                $add('PSU wattage (estimate)',(int)$psu>=$estimate?'compatible':'incompatible',"Estimated {$estimate}W required; PSU {$psu}W. This is not a connector check.");
            }
        }
        if(isset($parts['storage'],$parts['motherboard'])) $add('Storage / Motherboard ports','unknown','Motherboard storage-port specifications are not recorded.');
        $statuses=array_column($checks,'status');
        $overall=in_array('incompatible',$statuses,true)?'incompatible':'unknown';
        // Never claim whole-build compatibility: BIOS, connectors, clearance and other rules are not fully verified.
        return ['status'=>$overall,'checks'=>$checks,'selected_count'=>count($parts),'notice'=>'Preliminary checks only. Unknown means not verified; a green check is specific to its rule.'];
    }
}
