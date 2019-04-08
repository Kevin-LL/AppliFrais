<?php
$this->load->helper('url');
$this->load->helper('security');

// Extend the TCPDF class to create custom Header
class Imprimer extends TCPDF {
	
	// Page header
	public function Header()
	{
		// First page detection
		if ($this->page == 1)
		{
			// Logo
			$image_file = file_get_contents(img_url('logo.png'));
			$this->Image('@'.$image_file, 15, 10, 32, '', 'PNG', base_url('c_visiteur'), 'B', false, 300, '', false, false, 0, false, false, false);
			// Set font
			$this->SetFont('helvetica', 'BI', 20);
			// Title
			$this->Cell(0, 21, 'Gestion des frais de déplacements', 0, 0, 'C', 0, '', 0, false, 'B', 'M');
			// Separation
			$this->Ln(1);
			$style = array('width' => 0.5);
			$this->Line(15, $this->y, 195, $this->y, $style);
		}
	}
	
	// Page footer
	public function Footer()
	{
		// Position at 15 mm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('helvetica', '', 8);
		// Copyright
		$this->Cell(0, 10, 'Copyright © 2009-'.date("Y").' Laboratoire Galaxy-Swiss Bourdin', 0, 0, 'C', 0, '', 0, false, 'T', 'M');
	}
}

// create new PDF document
$pdf = new Imprimer('P', 'mm', 'A4', true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Laboratoire Galaxy-Swiss Bourdin');
$pdf->SetTitle('Imprimer');
$pdf->SetSubject('');
$pdf->SetKeywords('');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 9.5);

// add a page
$pdf->AddPage();

// create some HTML content with style
$html =
'<html>
	<head>
		<style>
			h1 {
				text-align: center;
				text-decoration: underline;
				font-size: 16px;
				font-weight: bold;
			}
			
			h2 {
				text-decoration: underline;
				font-size: 12px;
				font-weight: bold;
			}
			
			table.liste-legere {
				font-size: 11px;
			}
			
			table.liste-legere th {
				background-color: #7A7B91;
				text-align: center;
				font-weight: bold;
			}
			
			table.liste-legere .align-center {
				text-align: center;
			}
			
			table.liste-legere .align-left {
				text-align: left;
			}
			
			table.liste-legere .align-right {
				text-align: right;
			}
			
			.note {
				font-style: italic;
			}
			
			.label, .total {
				font-weight: bold;
			}
		</style>
	</head>
	<body>
		<h1>Fiche de frais du mois '.substr_replace($moisFiche, '-', 4, 0).'</h1>
		<p>
			<span class="label">Visiteur :</span> '.$this->session->userdata('idUser').' '.$this->session->userdata('nom').'<br />
			<span class="label">Etat :</span> '.$infosFiche['libEtat'].
		'</p>';
		if ($infosFiche['motifRefus'] != null)
		{
			$html.=
			'<p class="note">Note : cette fiche a été précédemment refusée.</p>';
		}
		$html.=
		'<h2>Eléments forfaitisés :</h2>';
		
		$aucunFraisForfaitDispo = true;
		
		foreach ($lesFraisForfait as $unFraisForfait)
		{
			if (isset($unFraisForfait['idfrais']))
			{
				$aucunFraisForfaitDispo = false;
			}
		}
		
		$fraisForfait =
		'<table class="liste-legere" border="1" cellpadding="3.5">
			<thead>
				<tr>
					<th>Libellé</th>
					<th>Quantité</th>
					<th>Montant</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>';
			foreach ($lesFraisForfait as $unFraisForfait)
			{
				$libelle = $unFraisForfait['libelle'];
				$quantite = $unFraisForfait['quantite'];
				$montant = $unFraisForfait['montant'];

				$fraisForfait.=
				'<tr>
					<td class="align-left">'.$libelle.'</td>
					<td class="align-left">'.xss_clean($quantite).'</td>
					<td class="align-right">'.xss_clean($montant).'€</td>
					<td class="align-right">'.number_format($quantite * $montant, 2).'€</td>
				</tr>';
			}
			$fraisForfait.=
			'</tbody>
		</table>';
		
		if ($aucunFraisForfaitDispo == true)
		{
			$fraisForfait =
			'<p>Aucun frais au forfait disponible.</p>';
		}
		
		$html.=
		$fraisForfait;
		
		$html.=
		'<h2>Elément(s) hors forfait :</h2>';
		
		$aucunFraisHorsForfaitDispo = true;
		
		foreach ($lesFraisHorsForfait as $unFraisHorsForfait)
		{
			if (isset($unFraisHorsForfait['id']))
			{
				$aucunFraisHorsForfaitDispo = false;
			}
		}
		
		$fraisHorsForfait =
		'<table class="liste-legere" border="1" cellpadding="3.5">
			<thead>
				<tr>
					<th>Date</th>
					<th>Libellé</th>
					<th>Montant</th>
					<th>Justificatif ['.$nbJustificatifs.']</th>
				</tr>
			</thead>
			<tbody>';
			foreach ($lesFraisHorsForfait as $unFraisHorsForfait)
			{
				$id = $unFraisHorsForfait['id'];
				$mois = $unFraisHorsForfait['mois'];
				$date = $unFraisHorsForfait['date'];
				$libelle = $unFraisHorsForfait['libelle'];
				$montant = $unFraisHorsForfait['montant'];
				$justificatifNom = $unFraisHorsForfait['justificatifNom'];
				$justificatifFichier = $unFraisHorsForfait['justificatifFichier'];
				$libEtat = ' ['.$unFraisHorsForfait['libEtat'].']';
				
				if ($justificatifFichier != null)
				{
					$justificatifNom = anchor('c_visiteur/telJustificatif/'.$mois.'/'.$id.'/'.$justificatifFichier, $justificatifNom);
				}
				else
				{
					$justificatifNom = 'Aucun';
				}
				
				$fraisHorsForfait.=
				'<tr>
					<td class="align-center">'.xss_clean($date).'</td>
					<td class="align-left">'.xss_clean($libelle).$libEtat.'</td>
					<td class="align-right">'.xss_clean($montant).'€</td>
					<td class="align-center">'.xss_clean($justificatifNom).'</td>
				</tr>';
			}
			$fraisHorsForfait.=
			'</tbody>
		</table>';
		
		if ($aucunFraisHorsForfaitDispo == true)
		{
			$fraisHorsForfait =
			'<p>Aucun frais hors forfait disponible.</p>';
		}
		
		$html.=
		$fraisHorsForfait;
		
		$html.=
		'<p><span class="total">TOTAL : '.$infosFiche['montantValide'].'€</span></p>
	</body>
</html>';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// ---------------------------------------------------------

// Close and output PDF document
$pdf->Output($moisFiche.'.pdf', 'I');
ob_end_flush();
?>