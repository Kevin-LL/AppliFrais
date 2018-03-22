<?php
$this->load->helper('url');

// Extend the TCPDF class to create custom Header
class MYPDF extends TCPDF {

    //Page header
    public function Header()
    {
        // Logo
        $image_file = file_get_contents(img_url('logo.png'));
        $this->Image('@'.$image_file, 15, 10, 32, '', 'PNG', '', 'B', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'BI', 20);
        // Title
        $this->Cell(0, 21, 'Gestion des frais de déplacements', 0, 0, 'C', 0, '', 0, false, 'B', 'M');
		// Separation
		$this->Ln(1);
		$style = array('width' => 0.5);
		$this->Line(15, $this->y, 195, $this->y, $style);
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
$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);

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
$pdf->SetFont('helvetica', '', 9.75);

// add a page
$pdf->AddPage();

// set some text to print
$html = '
<html>
	<head>
		<style>
			.title1 {
				font-size: 18px; 
				font-weight: bold; 
				text-align: center; 
				text-decoration: underline;
			}
			
			.title2 {
				font-weight: bold; 
				text-decoration: underline;
			}
			
			.bold {
				font-weight: bold; 
			}
			
			.italic {
				font-style: italic; 
			}
			
			th {
				background-color: #7A7B91;
				font-weight: bold;
			}
			
			.alignCenter {
				text-align: center;
			}

			.alignLeft {
				text-align: left;
			}

			.alignRight {
				text-align: right;
			}
		</style>
	</head>
	<body>
		<p>
			<span class="title1">Fiche de frais du mois '.$numAnnee.'-'.$numMois.'</span>
		</p>
		<p>
			<span class="bold">Identifiant :</span> '.$this->session->userdata('idUser').'<br>
			<span class="bold">Visiteur :</span> '.$this->session->userdata('prenom').' '.$this->session->userdata('nom').'<br>
			<span class="bold">Etat de la fiche :</span> '.$infosFiche['libEtat'].'';
			if (isset($infosFiche['motifRefus']))
			{
				if ($infosFiche['motifRefus'] != NULL)
				{
					$html = $html.'
					<br><br><span class="italic">Note : cette fiche a été précédemment refusée.</span>';
				}
			}
			$html.= '
		</p>
		<p>
			<span class="title2">Eléments forfaitisés :</span>
		</p>
		<table border="1" cellpadding="2">
			<thead>
				<tr>
					<th class="alignCenter">Libellé</th>
					<th class="alignCenter">Quantité</th>
					<th class="alignCenter">Montant</th>
					<th class="alignCenter">Total</th>
				</tr>
			</thead>
			<tbody>';
				foreach ($lesFraisForfait as $unFrais)
				{
					$idFrais = $unFrais['idfrais'];
					$libelle = $unFrais['libelle'];
					$quantite = $unFrais['quantite'];
					$montant = $unFrais['montant'];

					$html = $html.'
					<tr>
						<td class="alignLeft"><label for="'.$idFrais.'">'.$libelle.'</label></td>
						<td class="alignLeft"><label id="'.$idFrais.'" name="lesFrais['.$idFrais.']">'.$quantite.'</label></td>
						<td class="alignRight"><label id="montant'.$idFrais.'" name="lesMontants['.$idFrais.']">'.$montant.'€</label></td>
						<td class="alignRight"><label id="total'.$idFrais.'">'.number_format($quantite * $montant, 2).'€</label></td>
					</tr>';
				}
				$html.= '	
			</tbody>
		</table>
		<p>
			<span class="title2">Elément(s) hors forfait :</span>
		</p>
		<table border="1" cellpadding="2">
			<thead>
				<tr>
					<th class="alignCenter">Date</th>
					<th class="alignCenter">Libellé</th>
					<th class="alignCenter">Montant</th>
					<th class="alignCenter">Justificatif ['.$nbJustificatifs.']</th>
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
								
						if (isset($justificatifFichier))
						{
							if ($justificatifFichier != NULL)
							{
								$justificatifNom = anchor('c_visiteur/telJustificatif/'.$mois.'/'.$id.'/'.$justificatifFichier, $justificatifNom, 'class="anchorText" title="Télécharger le justificatif" download');
							}
							else
							{
								$justificatifNom = 'Aucun';
							}
						}

						$html = $html.'
						<tr>
							<td class="alignCenter">'.$date.'</td>
							<td class="alignLeft">'.$libelle.$libEtat.'</td>
							<td class="alignRight">'.$montant.'€</td>
							<td class="textData alignCenter" data-th="Justificatif">'.$justificatifNom.'</td>
						</tr>';
				}
				$html.= '
			</tbody>
		</table>
		<p>
			<span class="bold">TOTAL : '.$infosFiche['montantValide'].'€</span>
		</p>
	</body>
</html>';

// print a block of text using writeHTML()
$pdf->writeHTML($html, true, false, true, false, '');

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output($numAnnee.$numMois.'.pdf', 'I');
ob_end_flush();
?>