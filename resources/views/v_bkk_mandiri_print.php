<?php 

	// require_once ('../public/vendor/fpdf/fpdf.php');
	// require_once ('../public/vendor/fpdf/code128.php');
	require_once ('../public/vendor/Barcode.php');
	// $this->load->library('barcode');
	
	if(!function_exists("Terbilang")) {
	function Terbilang($x)
	{
		$x = abs($x);
	  	$abil = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		if ($x < 12)
			return " " . $abil[$x];
		elseif ($x < 20)
			return Terbilang($x - 10) . "belas";
		elseif ($x < 100)
			return Terbilang($x / 10) . " puluh" . Terbilang($x % 10);
		elseif ($x < 200)
			return " seratus" . Terbilang($x - 100);
		elseif ($x < 1000)
			return Terbilang($x / 100) . " ratus" . Terbilang($x % 100);
		elseif ($x < 2000)
			return " seribu" . Terbilang($x - 1000);
		elseif ($x < 1000000)
			return Terbilang($x / 1000) . " ribu" . Terbilang($x % 1000);
		elseif ($x < 1000000000)
			return Terbilang($x / 1000000) . " juta" . Terbilang($x % 1000000);
		elseif ($x >= 1000000000)
			return Terbilang($x / 1000000000) . " milyar" . Terbilang($x - ((floor($x/1000000000))*1000000000));
	}
}

	if(!class_exists("PDF")) {
	class PDF extends Barcode
	{	
		function print_kuitansionepage($project,$bkk_header,$detail_bkk,$tipe){

			$bulan = array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');

			$y = $this->getY()+5;
			$x = $this->getX();
			$this->SetY($y);
			// $this->SetDrawColor(255,0,0);
			$this->SetFont('times', 'B', 16);
			$this->SetDrawColor(187,53,197);
			$this->Cell(45,14,' ','LTRB',0,'C',0);
			$this->Cell(100,14,' ','LTRB',0,'C',0);
			$this->Cell(55,7,' ','LTRB',0,'C',0);
			$this->SetXY($x+45+95,$y+7);
			$this->Cell(55,7,' ','LTRB',0,'C',0);

			$this->SetY($y);
			if(isset($project['cop_header'])){
                if ($project['cop_header'] != '') {
                    $b = '../teknik/master/master_company/cop_header/'.$project['cop_header'];
                    $this->Image($b,10,$y+2,30,10);
                }
			}
			$this->Ln(2);
			
			$this->SetFont('times','B',15);
			// $this->SetTextColor(255,0,0);
			$this->SetTextColor(187,53,197);
			$this->Cell(46,14,'',0,0,'L');
			$this->Cell(98,14,'BUKTI PENGELUARAN KAS / BANK',0,0,'C');
			$this->SetFont('times','',9);
			$this->SetXY($x+32+108,$y);
			$this->Cell(6,7,'No : ',0,0,'L');
			$this->SetTextColor(255,0,0);
			$this->SetFont('times','',9);

			if ($bkk_header['name'] == '' || $bkk_header['name'] == '0' || $bkk_header['name'] == '/\s/' ) {
				$name = "BK/".substr($bkk_header['bank_initial'],0,4)."/".$bkk_header['initial']."/____/____/____";
			}else{
				$name = $bkk_header['name'];
			}
			$this->Cell(48,7,$name,0,1,'L');
			$this->SetTextColor(0,0,0);
			$this->SetXY($x+32+108,$y+7);
			$this->SetTextColor(187,53,197);
			// $this->SetTextColor(255,0,0);
			$this->Cell(8,7,'Tgl : ',0,0,'L');
			$this->SetTextColor(0,0,0);

			if ($bkk_header['tanggal'] == '' || $bkk_header['tanggal'] == '0' ) {
				$tanggal = '';
			}else{
				$tanggal = date_create($bkk_header['tanggal']);
				$tanggal = date_format($tanggal,"d-m-Y");
			}
			
			$this->Cell(48,7,$tanggal,0,1,'L');

			$this->SetTextColor(187,53,197);
			// $this->SetTextColor(255,0,0);

			$this->SetFont('times','',11);
			$y = $this->getY();
			$this->SetY($y);
			$this->Cell(200,33,' ','LTRB',0,'C',0);
			$this->Ln(1);
			$this->Cell(35,5,'Kas ','B',1,'L');
			$this->Cell(35,5,'Bank ',0,0,'L');
			$this->Cell(5,5,': ',0,0,'L');
			$this->SetTextColor(0,0,0);
			$this->Cell(55,5,$bkk_header['bank_name'].' - '.$bkk_header['rekening'],0,0,'L');

			//$this->SetTextColor(187,53,197);
			//$this->Cell(15,5,'Layer : ',0,0,'L');
			//$this->SetTextColor(0,0,0);
			//$this->Cell(45,5,$bkk_header['nama_layer'],0,1,'L');

			//$this->Cell(40,5,'',0,0,'L');
			// $this->SetTextColor(255,0,0);
			$this->SetTextColor(187,53,197);
			$this->ln(5);
			$this->Cell(95,5,'Disetorkan / Dibayarkan kepada : ',0,0,'R');
			$this->SetTextColor(0,0,0);			
			$this->Cell(50,5,$bkk_header['partner'],0,0,'L');
			$this->ln(5);

			$total_dpp = $bkk_header['total_dpp'];
			$total_ppn = $bkk_header['total_ppn'];
			$total_pph = $bkk_header['total_pph'];
			$total_payment = $total_dpp + $total_ppn - $total_pph;

			$this->Cell(40,5,'',0,0,'L');
			// $this->SetTextColor(255,0,0);
			$this->SetTextColor(187,53,197);
			$this->Cell(25,5,'Jumlah : ',0,0,'L');
			$this->SetTextColor(0,0,0);
			$this->Cell(55,5,'Rp '.number_format($total_payment,0,',','.'),0,1,'L');

			$this->ln(2);
			$this->Cell(40,5,'',0,0,'L');
			$this->SetTextColor(0,0,0);
			$this->MultiCell(155,5,ltrim(ucwords(Terbilang(ceil($total_payment)))).' Rupiah',0,'L');

			$y = $y+33;
			$this->SetY($y);
			// $this->SetTextColor(255,0,0);
			$this->SetTextColor(187,53,197);
			$this->Cell(15,7,'Account',1,0,'C');
			$this->Cell(40,7,'SPK',1,0,'C');
			$this->Cell(60,7,'Uraian',1,0,'C');
			$this->Cell(23,7,'DPP',1,0,'C');
			$this->Cell(19,7,'PPN',1,0,'C');
			// $this->Cell(28,7,'NO PPN',1,0,'C');
			$this->Cell(20,7,'PPH',1,0,'C');
			$this->Cell(23,7,'Jumlah',1,1,'C');

			$y = $this->getY();

			$this->SetFont('times','',9);
			$this->SetTextColor(0,0,0);

			$total_dpp = 0;
			$total_ppn = 0;
			$total_pph = 0;
			$total_jumlah = 0;
			$y1 = $y;
			foreach ($detail_bkk as $detail_bkk){
				$this->SetY($y1);
				$x_row = $this->getX();
				$y_row = $this->getY();

				$jumlah = $detail_bkk['dpp'] + $detail_bkk['ppn'] - $detail_bkk['pph'];

				$this->SetFont('times','',8);
				

				//$this->MultiCell(15,3,$detail_bkk['code'].' ('.$detail_bkk['init_layer'].')',0,'L');
				$this->MultiCell(15,4,$detail_bkk['code'].'  ='.$detail_bkk['init_layer'].'=',1,'L');
				
				//$this->SetX($x_row+80);
				//$y1 = $this->getY()+50;
				//$this->Cell($y1,8,$detail_bkk['init_layer'],'B',0,'L');	
				//$y1 = $this->getY()+50;

				$this->SetXY($x_row+15,$y_row);

				$this->Cell(40,4,$detail_bkk['spk_name'],0,1,'L');
				$this->SetX($x_row+15);
				$this->Cell(40,4,$detail_bkk['cop_name'],'B',0,'L');
				$y1 = $this->getY()+4;

				$height_multi = 8;$kavling = '';$serial = '';
				if ($detail_bkk['kavling_name']=='' || $detail_bkk['kavling_name']==NULL){
					$kavling = '';
				}
				else{
					$kavling = $detail_bkk['kavling_name'];
				}
				
				if ($detail_bkk['serial']=='' || $detail_bkk['serial']==NULL){
					$serial = '';
				}
				else{
					if ($detail_bkk['coa_id']==1229 || $detail_bkk['coa_id']==1404){
						$serial = '(T-'.$detail_bkk['serial'].')';
					}
				}
				
				//$type_of_work = substr($serial.' '.$detail_bkk['pekerjaan'].' '.$kavling,0,80);
				$type_of_work = substr($serial.' '.trim(preg_replace('/\s+/', ' ', $detail_bkk['pekerjaan'])),0,80);
				if (strlen($type_of_work) > 35) {
				 	$height_multi = 4;
				}

				$this->SetFont('times','',9);
				$this->SetXY($x_row+55,$y_row);
				$this->MultiCell(60,$height_multi,$type_of_work,0,'L');

				$this->SetXY($x_row+55,$y_row);
				$this->Cell(60,8,'','B',0,'L');

				$this->SetXY($x_row+55+60,$y_row);
				$this->Cell(23,8,number_format($detail_bkk['dpp']*1,0,',','.'),'B',0,'R');

				$this->Cell(19,8,number_format($detail_bkk['ppn']*1,0,',','.'),'B',0,'R');
				
				$this->SetFont('times','',9);
				$this->SetXY($x_row+55+60+23+19,$y_row);
				$this->Cell(20,4,number_format($detail_bkk['pph']*1,0,',','.'),'',1,'R');

				$this->SetX($x_row+55+60+23+19);
				$this->SetFont('times','',8);
				$this->Cell(20,4,$detail_bkk['pph_tipe'],'B',0,'R');

				$this->SetXY($x_row+55+60+23+19+20,$y_row);

				$this->SetFont('times','',9);
				$this->Cell(23,8,number_format($jumlah,0,',','.'),'B',1,'R');

				$total_dpp += $detail_bkk['dpp'];
				$total_ppn += $detail_bkk['ppn'];
				$total_pph += $detail_bkk['pph'];
				$total_jumlah += $jumlah;
			}

			$this->SetY($y+45);
			$this->SetFont('times','B',9);
			$this->Cell(115,5,'Total','LR',0,'R');
			$this->Cell(23,5,number_format($total_dpp,0,',','.'),'LR',0,'R');
			$this->Cell(19,5,number_format($total_ppn,0,',','.'),'LR',0,'R');
			// $this->Cell(28,5,'','LR',0,'R');
			$this->Cell(20,5,number_format($total_pph,0,',','.'),'LR',0,'R');
			$this->Cell(23,5,number_format($total_jumlah,0,',','.'),'LR',1,'R');

			// detail
			$this->SetY($y);
			$this->Cell(15,50,' ','LTRB',0,'C',0);
			$this->Cell(40,50,' ','LTRB',0,'C',0);
			$this->Cell(60,50,' ','LTRB',0,'C',0);
			$this->Cell(23,50,' ','LTRB',0,'C',0);
			$this->Cell(19,50,' ','LTRB',0,'C',0);
			// $this->Cell(28,50,' ','LTRB',0,'C',0);
			$this->Cell(20,50,' ','LTRB',0,'C',0);
			$this->Cell(23,50,' ','LTRB',1,'C',0);

			$this->SetFont('times','',11);
			$this->SetTextColor(0,0,0);
			$this->Ln(1);
			$this->SetX($x-5);
			$this->Cell(40,5,'',0,0,'L');
			// tanda tangan
			$y = $this->getY();
			$this->SetFont('times','',9);
			$this->SetTextColor(187,53,197);
			// $this->SetTextColor(255,0,0);
			$this->SetX($x-5);
			$this->Cell(80,5,'Catatan : ',0,0,'L',0);
			$this->Cell(30,5,'Disetujui ',1,0,'C',0);
			$this->Cell(30,5,'Diperiksa ',1,0,'C',0);
			$this->Cell(30,5,'Dibukukan ',1,0,'C',0);
			$this->Cell(30,5,'Penerima ',1,1,'C',0);

			$this->SetTextColor(0,0,0);
			$this->MultiCell(80,5,$bkk_header['uraian'],0,'L');
			$this->SetTextColor(187,53,197);

			// $y = $this->getY();
			$this->SetX($x-5);
			$this->SetY($y);
			
			$this->Cell(80,25,' ','LTRB',0,'C',0);
			$this->Cell(30,25,'','LTRB',0,'C',0);
			//$this->MultiCell(30,25,$bkk_header['otorisasi_tanggal'].$bkk_header['otorisasi_by'],'LTRB',0,'C',0);
			if ($bkk_header['otorisasi'] == 0){
				$this->Cell(30,25,'NOT','LTRB',0,'C',0);
			}
			else{
				$this->Cell(30,25,$bkk_header['otorisasi_oleh'],'LTRB',0,'C',0);
			}
			
			$this->Cell(30,25,$bkk_header['created_oleh'],'LTRB',0,'C',0);
			$this->Cell(30,25,' ','LTRB',0,'C',0);
			
			$this->Ln(4);

			$this->Cell(80,25,' ','',0,'C',0);
			$this->Cell(30,25,'','',0,'C',0);
			//$this->MultiCell(30,25,$bkk_header['otorisasi_tanggal'].$bkk_header['otorisasi_by'],'LTRB',0,'C',0);
			if ($bkk_header['otorisasi'] == 0){
				$this->Cell(30,25,'AUTHORIZED','',0,'C',0);				
			}
			else{
				$this->Cell(30,25,$bkk_header['oto_tgl'],'',0,'C',0);
			}
			
			$this->Cell(30,25,$bkk_header['created_tgl'],'',0,'C',0);
			$this->Cell(30,25,' ','',0,'C',0);

			$this->Ln(4);

			$this->Cell(80,25,' ','',0,'C',0);
			$this->Cell(30,25,'','',0,'C',0);
			//$this->MultiCell(30,25,$bkk_header['otorisasi_tanggal'].$bkk_header['otorisasi_by'],'LTRB',0,'C',0);
			$this->Cell(30,25,$bkk_header['oto_jam'],'',0,'C',0);
			$this->Cell(30,25,$bkk_header['created_jam'],'',0,'C',0);
			$this->Cell(30,25,' ','',0,'C',0);


			// barcode
			// $code=strtoupper($tipe)."-1234567890";
			$this->SetTextColor(0,0,0);
			$code=$tipe."-".$bkk_header['id'];
			$this->Code128(155,1,$code,50,10);
			// $this->Code39(140,1,$code,1,9);
			$this->SetXY(155,11);
			$this->Cell(50,5,$code,0,0,'C',0);
		}

		function print_kuitansi($page,$totalpage,$project,$bkk_header,$detail_bkk,$tipe){

			$bulan = array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');

			$y = $this->getY()+5;
			$x = $this->getX();
			$this->SetY($y);
			// $this->SetDrawColor(255,0,0);
			$this->SetDrawColor(187,53,197);
			$this->Cell(45,14,' ','LTRB',0,'C',0);
			$this->Cell(100,14,' ','LTRB',0,'C',0);
			$this->Cell(55,7,' ','LTRB',0,'C',0);
			$this->SetXY($x+45+95,$y+7);
			$this->Cell(55,7,' ','LTRB',0,'C',0);

			$this->SetY($y);
			if(isset($project['cop_header'])){
                if ($project['cop_header'] != '') {
                    $b = '../teknik/master/master_company/cop_header/'.$project['cop_header'];
                    $this->Image($b,10,$y+2,30,10);
                }
			}
			$this->Ln(2);
			
			$this->SetFont('times','B',15);
			// $this->SetTextColor(255,0,0);
			$this->SetTextColor(187,53,197);
			$this->Cell(46,14,'',0,0,'L');
			$this->Cell(98,14,'BUKTI PENGELUARAN KAS / BANK',0,0,'C');
			$this->SetFont('times','',9);
			$this->SetXY($x+32+108,$y);
			$this->Cell(6,7,'No : ',0,0,'L');
			$this->SetTextColor(255,0,0);
			$this->SetFont('times','',9);

			if ($bkk_header['name'] == '' || $bkk_header['name'] == '0' || $bkk_header['name'] == '/\s/' ) {
				$name = "BK/".substr($bkk_header['bank_initial'],0,4)."/".$bkk_header['initial']."/____/____/____";
			}else{
				$name = $bkk_header['name'];
			}
			$this->Cell(48,7,$name,0,1,'L');
			$this->SetTextColor(0,0,0);
			$this->SetXY($x+32+108,$y+7);
			$this->SetTextColor(187,53,197);
			// $this->SetTextColor(255,0,0);
			$this->Cell(8,7,'Tgl : ',0,0,'L');
			$this->SetTextColor(0,0,0);

			if ($bkk_header['tanggal'] == '' || $bkk_header['tanggal'] == '0' ) {
				$tanggal = '';
			}else{
				$tanggal = date_create($bkk_header['tanggal']);
				$tanggal = date_format($tanggal,"d-m-Y");
			}
			
			$this->Cell(48,7,$tanggal,0,1,'L');

			$this->SetTextColor(187,53,197);
			// $this->SetTextColor(255,0,0);

			$this->SetFont('times','',11);
			$y = $this->getY();
			$this->SetY($y);
			$this->Cell(200,33,' ','LTRB',0,'C',0);
			$this->Ln(1);
			$this->Cell(35,5,'Kas ','B',1,'L');
			$this->Cell(35,5,'Bank ',0,0,'L');
			$this->Cell(5,5,': ',0,0,'L');
			$this->SetTextColor(0,0,0);
			$this->Cell(55,5,$bkk_header['bank_name'].' - '.$bkk_header['rekening'],0,0,'L');

			//$this->SetTextColor(187,53,197);
			//$this->Cell(15,5,'Layer : ',0,0,'L');
			//$this->SetTextColor(0,0,0);
			//$this->Cell(45,5,$bkk_header['nama_layer'],0,1,'L');

			//$this->Cell(40,5,'',0,0,'L');
			// $this->SetTextColor(255,0,0);
			$this->SetTextColor(187,53,197);
			$this->ln(5);
			$this->Cell(95,5,'Disetorkan / Dibayarkan kepada : ',0,0,'R');
			$this->SetTextColor(0,0,0);			
			$this->Cell(50,5,$bkk_header['partner'],0,0,'L');
			$this->ln(5);

			$total_dpp = $bkk_header['total_dpp'];
			$total_ppn = $bkk_header['total_ppn'];
			$total_pph = $bkk_header['total_pph'];
			$total_payment = $total_dpp + $total_ppn - $total_pph;

			$this->Cell(40,5,'',0,0,'L');
			// $this->SetTextColor(255,0,0);
			$this->SetTextColor(187,53,197);
			$this->Cell(25,5,'Jumlah : ',0,0,'L');
			$this->SetTextColor(0,0,0);
			$this->Cell(55,5,'Rp '.number_format($total_payment,0,',','.'),0,1,'L');

			$this->ln(2);
			$this->Cell(40,5,'',0,0,'L');
			$this->SetTextColor(0,0,0);
			$this->MultiCell(155,5,ltrim(ucwords(Terbilang(ceil($total_payment)))).' Rupiah',0,'L');

			$y = $y+33;
			$this->SetY($y);
			// $this->SetTextColor(255,0,0);
			$this->SetTextColor(187,53,197);
			$this->Cell(15,7,'Account',1,0,'C');
			$this->Cell(40,7,'SPK',1,0,'C');
			$this->Cell(60,7,'Uraian',1,0,'C');
			$this->Cell(23,7,'DPP',1,0,'C');
			$this->Cell(19,7,'PPN',1,0,'C');
			// $this->Cell(28,7,'NO PPN',1,0,'C');
			$this->Cell(20,7,'PPH',1,0,'C');
			$this->Cell(23,7,'Jumlah',1,1,'C');

			$y = $this->getY();

			$this->SetFont('times','',9);
			$this->SetTextColor(0,0,0);

			$total_dpp = 0;
			$total_ppn = 0;
			$total_pph = 0;
			$total_jumlah = 0;
			$y1 = $y;
			foreach ($detail_bkk as $detail_bkk){
				$this->SetY($y1);
				$x_row = $this->getX();
				$y_row = $this->getY();

				$jumlah = $detail_bkk['dpp'] + $detail_bkk['ppn'] - $detail_bkk['pph'];

				$this->SetFont('times','',8);
				

				//$this->MultiCell(15,3,$detail_bkk['code'].' ('.$detail_bkk['init_layer'].')',0,'L');
				$this->MultiCell(15,4,$detail_bkk['code'].'  ='.$detail_bkk['init_layer'].'=',1,'L');
				
				//$this->SetX($x_row+80);
				//$y1 = $this->getY()+50;
				//$this->Cell($y1,8,$detail_bkk['init_layer'],'B',0,'L');	
				//$y1 = $this->getY()+50;

				$this->SetXY($x_row+15,$y_row);

				$this->Cell(40,4,$detail_bkk['spk_name'],0,1,'L');
				$this->SetX($x_row+15);
				$this->Cell(40,4,$detail_bkk['cop_name'],'B',0,'L');
				$y1 = $this->getY()+4;

				$height_multi = 8;$kavling = '';$serial = '';
				if ($detail_bkk['kavling_name']=='' || $detail_bkk['kavling_name']==NULL){
					$kavling = '';
				}
				else{
					$kavling = $detail_bkk['kavling_name'];
				}
				
				if ($detail_bkk['serial']=='' || $detail_bkk['serial']==NULL){
					$serial = '';
				}
				else{
					if ($detail_bkk['coa_id']==1229 || $detail_bkk['coa_id']==1404){
						$serial = '(T-'.$detail_bkk['serial'].')';
					}
				}
				
				//$type_of_work = substr($serial.' '.$detail_bkk['pekerjaan'].' '.$kavling,0,80);
				$type_of_work = substr($serial.' '.trim(preg_replace('/\s+/', ' ', $detail_bkk['pekerjaan'])),0,80);
				if (strlen($type_of_work) > 35) {
				 	$height_multi = 4;
				}

				$this->SetFont('times','',9);
				$this->SetXY($x_row+55,$y_row);
				$this->MultiCell(60,$height_multi,$type_of_work,0,'L');

				$this->SetXY($x_row+55,$y_row);
				$this->Cell(60,8,'','B',0,'L');

				$this->SetXY($x_row+55+60,$y_row);
				$this->Cell(23,8,number_format($detail_bkk['dpp']*1,0,',','.'),'B',0,'R');

				$this->Cell(19,8,number_format($detail_bkk['ppn']*1,0,',','.'),'B',0,'R');
				
				$this->SetFont('times','',9);
				$this->SetXY($x_row+55+60+23+19,$y_row);
				$this->Cell(20,4,number_format($detail_bkk['pph']*1,0,',','.'),'',1,'R');

				$this->SetX($x_row+55+60+23+19);
				$this->SetFont('times','',8);
				$this->Cell(20,4,$detail_bkk['pph_tipe'],'B',0,'R');

				$this->SetXY($x_row+55+60+23+19+20,$y_row);

				$this->SetFont('times','',9);
				$this->Cell(23,8,number_format($jumlah,0,',','.'),'B',1,'R');

			}
			// detail
			$this->SetY($y);
			$this->Cell(15,70,' ','LTRB',0,'C',0);
			$this->Cell(40,70,' ','LTRB',0,'C',0);
			$this->Cell(60,70,' ','LTRB',0,'C',0);
			$this->Cell(23,70,' ','LTRB',0,'C',0);
			$this->Cell(19,70,' ','LTRB',0,'C',0);
			// $this->Cell(28,50,' ','LTRB',0,'C',0);
			$this->Cell(20,70,' ','LTRB',0,'C',0);
			$this->Cell(23,70,' ','LTRB',1,'C',0);

			// barcode
			// $code=strtoupper($tipe)."-1234567890";
			$this->SetTextColor(0,0,0);
			$code=$tipe."-".$bkk_header['id'];
			$this->Code128(155,1,$code,50,10);
			// $this->Code39(140,1,$code,1,9);
			$ket="PAGE ".$page." / ".$totalpage;
			$this->SetXY(5,5);
			$this->Cell(10,5,$ket,0,0,'L',0);
			$this->SetXY(155,11);
			$this->Cell(50,5,$code,0,0,'C',0);
		}

		function print_midpage($page,$totalpage,$project,$bkk_header,$detail_bkk,$tipe){

			$bulan = array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');

			$y = $this->getY()+5;
			$x = $this->getX()+5;
			$this->SetY($y);
			// $this->SetTextColor(255,0,0);
			$this->SetTextColor(187,53,197);
			$this->Cell(15,7,'Account',1,0,'C');
			$this->Cell(40,7,'SPK',1,0,'C');
			$this->Cell(60,7,'Uraian',1,0,'C');
			$this->Cell(23,7,'DPP',1,0,'C');
			$this->Cell(19,7,'PPN',1,0,'C');
			// $this->Cell(28,7,'NO PPN',1,0,'C');
			$this->Cell(20,7,'PPH',1,0,'C');
			$this->Cell(23,7,'Jumlah',1,1,'C');

			$y = $this->getY();

			$this->SetFont('times','',9);
			$this->SetTextColor(0,0,0);

			// $total_dpp = 0;
			// $total_ppn = 0;
			// $total_pph = 0;
			// $total_jumlah = 0;
			$y1 = $y;
			foreach ($detail_bkk as $detail_bkk){
				$this->SetY($y1);
				$x_row = $this->getX();
				$y_row = $this->getY();

				$jumlah = $detail_bkk['dpp'] + $detail_bkk['ppn'] - $detail_bkk['pph'];

				$this->SetFont('times','',8);
				

				//$this->MultiCell(15,3,$detail_bkk['code'].' ('.$detail_bkk['init_layer'].')',0,'L');
				$this->MultiCell(15,4,$detail_bkk['code'].'  ='.$detail_bkk['init_layer'].'=',1,'L');
				
				//$this->SetX($x_row+80);
				//$y1 = $this->getY()+50;
				//$this->Cell($y1,8,$detail_bkk['init_layer'],'B',0,'L');	
				//$y1 = $this->getY()+50;

				$this->SetXY($x_row+15,$y_row);

				$this->Cell(40,4,$detail_bkk['spk_name'],0,1,'L');
				$this->SetX($x_row+15);
				$this->Cell(40,4,$detail_bkk['cop_name'],'B',0,'L');
				$y1 = $this->getY()+4;

				$height_multi = 8;$kavling = '';$serial = '';
				if ($detail_bkk['kavling_name']=='' || $detail_bkk['kavling_name']==NULL){
					$kavling = '';
				}
				else{
					$kavling = $detail_bkk['kavling_name'];
				}
				
				if ($detail_bkk['serial']=='' || $detail_bkk['serial']==NULL){
					$serial = '';
				}
				else{
					if ($detail_bkk['coa_id']==1229 || $detail_bkk['coa_id']==1404){
						$serial = '(T-'.$detail_bkk['serial'].')';
					}
				}
				
				//$type_of_work = substr($serial.' '.$detail_bkk['pekerjaan'].' '.$kavling,0,80);
				$type_of_work = substr($serial.' '.trim(preg_replace('/\s+/', ' ', $detail_bkk['pekerjaan'])),0,80);
				if (strlen($type_of_work) > 35) {
				 	$height_multi = 4;
				}

				$this->SetFont('times','',9);
				$this->SetXY($x_row+55,$y_row);
				$this->MultiCell(60,$height_multi,$type_of_work,0,'L');

				$this->SetXY($x_row+55,$y_row);
				$this->Cell(60,8,'','B',0,'L');

				$this->SetXY($x_row+55+60,$y_row);
				$this->Cell(23,8,number_format($detail_bkk['dpp']*1,0,',','.'),'B',0,'R');

				$this->Cell(19,8,number_format($detail_bkk['ppn']*1,0,',','.'),'B',0,'R');
				
				$this->SetFont('times','',9);
				$this->SetXY($x_row+55+60+23+19,$y_row);
				$this->Cell(20,4,number_format($detail_bkk['pph']*1,0,',','.'),'',1,'R');

				$this->SetX($x_row+55+60+23+19);
				$this->SetFont('times','',8);
				$this->Cell(20,4,$detail_bkk['pph_tipe'],'B',0,'R');

				$this->SetXY($x_row+55+60+23+19+20,$y_row);

				$this->SetFont('times','',9);
				$this->Cell(23,8,number_format($jumlah,0,',','.'),'B',1,'R');

			}
			//$this->SetY($y+85);
			// detail
			$this->SetY($y);
			$this->Cell(15,90,' ','LTRB',0,'C',0);
			$this->Cell(40,90,' ','LTRB',0,'C',0);
			$this->Cell(60,90,' ','LTRB',0,'C',0);
			$this->Cell(23,90,' ','LTRB',0,'C',0);
			$this->Cell(19,90,' ','LTRB',0,'C',0);
			// $this->Cell(28,50,' ','LTRB',0,'C',0);
			$this->Cell(20,90,' ','LTRB',0,'C',0);
			$this->Cell(23,90,' ','LTRB',1,'C',0);

			// barcode
			// $code=strtoupper($tipe)."-1234567890";
			$this->SetTextColor(0,0,0);
			$code=$tipe."-".$bkk_header['id'];
			$this->Code128(155,1,$code,50,10);
			// $this->Code39(140,1,$code,1,9);
			$ket="PAGE ".$page." / ".$totalpage;
			$this->SetXY(5,5);
			$this->Cell(10,5,$ket,0,0,'L',0);
			$this->SetXY(155,11);
			$this->Cell(50,5,$code,0,0,'C',0);
		}

		function print_lastpage($page,$totalpage,$project,$bkk_header,$detail_bkk,$tipe){

			$bulan = array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');

			$y = $this->getY()+5;
			$x = $this->getX()+5;
			$this->SetY($y);
			// $this->SetTextColor(255,0,0);
			$this->SetTextColor(187,53,197);
			$this->Cell(15,7,'Account',1,0,'C');
			$this->Cell(40,7,'SPK',1,0,'C');
			$this->Cell(60,7,'Uraian',1,0,'C');
			$this->Cell(23,7,'DPP',1,0,'C');
			$this->Cell(19,7,'PPN',1,0,'C');
			// $this->Cell(28,7,'NO PPN',1,0,'C');
			$this->Cell(20,7,'PPH',1,0,'C');
			$this->Cell(23,7,'Jumlah',1,1,'C');

			$y = $this->getY();

			$this->SetFont('times','',9);
			$this->SetTextColor(0,0,0);

			// $total_dpp = 0;
			// $total_ppn = 0;
			// $total_pph = 0;
			// $total_jumlah = 0;
			$y1 = $y;
			foreach ($detail_bkk as $detail_bkk){
				$this->SetY($y1);
				$x_row = $this->getX();
				$y_row = $this->getY();

				$jumlah = $detail_bkk['dpp'] + $detail_bkk['ppn'] - $detail_bkk['pph'];

				$this->SetFont('times','',8);
				

				//$this->MultiCell(15,3,$detail_bkk['code'].' ('.$detail_bkk['init_layer'].')',0,'L');
				$this->MultiCell(15,4,$detail_bkk['code'].'  ='.$detail_bkk['init_layer'].'=',1,'L');
				
				//$this->SetX($x_row+80);
				//$y1 = $this->getY()+50;
				//$this->Cell($y1,8,$detail_bkk['init_layer'],'B',0,'L');	
				//$y1 = $this->getY()+50;

				$this->SetXY($x_row+15,$y_row);

				$this->Cell(40,4,$detail_bkk['spk_name'],0,1,'L');
				$this->SetX($x_row+15);
				$this->Cell(40,4,$detail_bkk['cop_name'],'B',0,'L');
				$y1 = $this->getY()+4;

				$height_multi = 8;$kavling = '';$serial = '';
				if ($detail_bkk['kavling_name']=='' || $detail_bkk['kavling_name']==NULL){
					$kavling = '';
				}
				else{
					$kavling = $detail_bkk['kavling_name'];
				}
				
				if ($detail_bkk['serial']=='' || $detail_bkk['serial']==NULL){
					$serial = '';
				}
				else{
					if ($detail_bkk['coa_id']==1229 || $detail_bkk['coa_id']==1404){
						$serial = '(T-'.$detail_bkk['serial'].')';
					}
				}
				
				//$type_of_work = substr($serial.' '.$detail_bkk['pekerjaan'].' '.$kavling,0,80);
				$type_of_work = substr($serial.' '.trim(preg_replace('/\s+/', ' ', $detail_bkk['pekerjaan'])),0,80);
				if (strlen($type_of_work) > 35) {
				 	$height_multi = 4;
				}

				$this->SetFont('times','',9);
				$this->SetXY($x_row+55,$y_row);
				$this->MultiCell(60,$height_multi,$type_of_work,0,'L');

				$this->SetXY($x_row+55,$y_row);
				$this->Cell(60,8,'','B',0,'L');

				$this->SetXY($x_row+55+60,$y_row);
				$this->Cell(23,8,number_format($detail_bkk['dpp']*1,0,',','.'),'B',0,'R');

				$this->Cell(19,8,number_format($detail_bkk['ppn']*1,0,',','.'),'B',0,'R');
				
				$this->SetFont('times','',9);
				$this->SetXY($x_row+55+60+23+19,$y_row);
				$this->Cell(20,4,number_format($detail_bkk['pph']*1,0,',','.'),'',1,'R');

				$this->SetX($x_row+55+60+23+19);
				$this->SetFont('times','',8);
				$this->Cell(20,4,$detail_bkk['pph_tipe'],'B',0,'R');

				$this->SetXY($x_row+55+60+23+19+20,$y_row);

				$this->SetFont('times','',9);
				$this->Cell(23,8,number_format($jumlah,0,',','.'),'B',1,'R');

				// $total_dpp += $detail_bkk['dpp'];
				// $total_ppn += $detail_bkk['ppn'];
				// $total_pph += $detail_bkk['pph'];
				// $total_jumlah += $jumlah;
			}
			
			$total_dpp = $bkk_header['total_dpp'];
			$total_ppn = $bkk_header['total_ppn'];
			$total_pph = $bkk_header['total_pph'];
			$total_jumlah = $total_dpp + $total_ppn - $total_pph;
			
			$this->SetY($y+85);
			$this->SetFont('times','B',9);
			$this->Cell(115,5,'Total','LR',0,'R');
			$this->Cell(23,5,number_format($total_dpp,0,',','.'),'LR',0,'R');
			$this->Cell(19,5,number_format($total_ppn,0,',','.'),'LR',0,'R');
			// $this->Cell(28,5,'','LR',0,'R');
			$this->Cell(20,5,number_format($total_pph,0,',','.'),'LR',0,'R');
			$this->Cell(23,5,number_format($total_jumlah,0,',','.'),'LR',1,'R');

			// detail
			$this->SetY($y);
			$this->Cell(15,90,' ','LTRB',0,'C',0);
			$this->Cell(40,90,' ','LTRB',0,'C',0);
			$this->Cell(60,90,' ','LTRB',0,'C',0);
			$this->Cell(23,90,' ','LTRB',0,'C',0);
			$this->Cell(19,90,' ','LTRB',0,'C',0);
			// $this->Cell(28,50,' ','LTRB',0,'C',0);
			$this->Cell(20,90,' ','LTRB',0,'C',0);
			$this->Cell(23,90,' ','LTRB',1,'C',0);

			$this->SetFont('times','',11);
			$this->SetTextColor(0,0,0);
			$this->Ln(1);
			$this->SetX($x-5);
			$this->Cell(40,5,'',0,0,'L');
			// tanda tangan
			$y = $this->getY();
			$this->SetFont('times','',9);
			$this->SetTextColor(187,53,197);
			// $this->SetTextColor(255,0,0);
			$this->SetX($x-5);
			$this->Cell(80,5,'Catatan : ',0,0,'L',0);
			$this->Cell(30,5,'Disetujui ',1,0,'C',0);
			$this->Cell(30,5,'Diperiksa ',1,0,'C',0);
			$this->Cell(30,5,'Dibukukan ',1,0,'C',0);
			$this->Cell(30,5,'Penerima ',1,1,'C',0);

			$this->SetTextColor(0,0,0);
			$this->MultiCell(80,5,$bkk_header['uraian'],0,'L');
			$this->SetTextColor(187,53,197);

			// $y = $this->getY();
			$this->SetX($x-5);
			$this->SetY($y);
			
			$this->Cell(80,25,' ','LTRB',0,'C',0);
			$this->Cell(30,25,'','LTRB',0,'C',0);
			//$this->MultiCell(30,25,$bkk_header['otorisasi_tanggal'].$bkk_header['otorisasi_by'],'LTRB',0,'C',0);
			if ($bkk_header['otorisasi'] == 0){
				$this->Cell(30,25,'NOT','LTRB',0,'C',0);
			}
			else{
				$this->Cell(30,25,$bkk_header['otorisasi_oleh'],'LTRB',0,'C',0);
			}
			
			$this->Cell(30,25,$bkk_header['created_oleh'],'LTRB',0,'C',0);
			$this->Cell(30,25,' ','LTRB',0,'C',0);
			
			$this->Ln(4);

			$this->Cell(80,25,' ','',0,'C',0);
			$this->Cell(30,25,'','',0,'C',0);
			//$this->MultiCell(30,25,$bkk_header['otorisasi_tanggal'].$bkk_header['otorisasi_by'],'LTRB',0,'C',0);
			if ($bkk_header['otorisasi'] == 0){
				$this->Cell(30,25,'AUTHORIZED','',0,'C',0);				
			}
			else{
				$this->Cell(30,25,$bkk_header['oto_tgl'],'',0,'C',0);
			}
			
			$this->Cell(30,25,$bkk_header['created_tgl'],'',0,'C',0);
			$this->Cell(30,25,' ','',0,'C',0);

			$this->Ln(4);

			$this->Cell(80,25,' ','',0,'C',0);
			$this->Cell(30,25,'','',0,'C',0);
			//$this->MultiCell(30,25,$bkk_header['otorisasi_tanggal'].$bkk_header['otorisasi_by'],'LTRB',0,'C',0);
			$this->Cell(30,25,$bkk_header['oto_jam'],'',0,'C',0);
			$this->Cell(30,25,$bkk_header['created_jam'],'',0,'C',0);
			$this->Cell(30,25,' ','',0,'C',0);


			// barcode
			// $code=strtoupper($tipe)."-1234567890";
			$this->SetTextColor(0,0,0);
			$code=$tipe."-".$bkk_header['id'];
			$this->Code128(155,1,$code,50,10);
			// $this->Code39(140,1,$code,1,9);
			$ket="PAGE ".$page." / ".$totalpage;
			$this->SetXY(5,5);
			$this->Cell(10,5,$ket,0,0,'L',0);
			$this->SetXY(155,11);
			$this->Cell(50,5,$code,0,0,'C',0);
		}

	}
}

	foreach ($project_company as $project_company);
	foreach ($bkk_header as $bkk_header);

	$pdf=new PDF('L','mm','A5');
	$row = count($detail_bkk);
	if ($row < 7){
		$pdf->AddPage();
		$pdf->SetLeftMargin(5);
		// $pdf->SetTopMargin(70);
		$pdf->SetAutoPageBreak(false);
		// $pdf->SetBottonMargin(-10);
		$pdf->print_kuitansionepage($project_company,$bkk_header,$detail_bkk,$tipe);
	} else {
		$newdetail_bkk = array_chunk($detail_bkk, 7, true);
		$j = count($newdetail_bkk);
		for($i=0;$i<$j;$i++){
			$pdf->AddPage();
			$pdf->SetLeftMargin(5);
			// $pdf->SetTopMargin(70);
			$pdf->SetAutoPageBreak(false);
			// $pdf->SetBottonMargin(-10);
			if ($i < ($j-1)){
				$no = $i + 1;
				$pdf->print_kuitansi($no,$j,$project_company,$bkk_header,$newdetail_bkk[$i],$tipe);
			} else if ($i == ($j-1)){
				$no = $i + 1;
				$pdf->print_lastpage($no,$j,$project_company,$bkk_header,$newdetail_bkk[$i],$tipe);
			} else {
				$no = $i + 1;
				$pdf->print_midpage($no,$j,$project_company,$bkk_header,$newdetail_bkk[$i],$tipe);
			}		
		}
	}

	// $pdf->AddPage();
	// $code='12345';
	// $pdf->Code128(50,70,$code,45,15);
	// $pdf->SetXY(50,85);
	// $pdf->Write(5,$code);

	// $pdf->AddPage();
	// $pdf->SetLeftMargin(10);
	// $pdf->SetTopMargin(10);
	// $pdf->print_kuitansi_copy($project,$order,$payment,$history_transaksi);

	// $pdf->AddPage();
	// $pdf->SetLeftMargin(10);
	// $pdf->SetTopMargin(10);
	// $pdf->print_bm($project,$order,$payment,$history_transaksi);
	$pdf->Output();
?>
