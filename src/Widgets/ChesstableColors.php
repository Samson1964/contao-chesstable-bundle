<?php

namespace Schachbulle\ContaoChesstableBundle\Widgets;

class ChesstableColors extends \Widget
{
	// Anleitung: https://alexandernaumov.de/artikel/contao-backend-widget

	/**
	 * @var bool
	 */
	protected $blnSubmitInput = true;
	
	/**
	 * @var string
	 */
	protected $strTemplate = 'be_widget';
	
	/**
	 * @param mixed $varInput
	 * @return mixed
	 */
	protected function validator($varInput)
	{
		$varInput = serialize((array)$varInput);

		//echo "hallo|";
		//print_r($varInput);
		//echo "|";
		//exit;
		return parent::validator($varInput);
	}
	
	/**
	 * @return string
	 */
	public function generate()
	{

		// Voreinstellungen laden
		$configColors = (array)unserialize($GLOBALS['TL_CONFIG']['chesstable_markColors']);
		// Daten aus dem Inhaltselement laden
		$configRows = is_array($this->varValue) ? $this->varValue : (array)unserialize($this->varValue);
		//$configRows = $this->varValue;

		// Daten aus Einstellungen in Ausgabe-Array übertragen
		$ausgabe = array();
		foreach($configColors as $item)
		{
			$ausgabe[$item['intern']] = array
			(
				'name'    => $item['name'],
				'color'   => '#'.$item['color'],
				'rows'    => '',
				'flags'   => '',
				'defined' => true,
			);
		}
		
		// Daten aus Inhaltselement in Ausgabe-Array übertragen
		if(count($configRows) > 0)
		{
			foreach($configRows as $item)
			{
				if($ausgabe[$item['intern']])
				{
					// Datensatz ist vorkonfiguriert
					$ausgabe[$item['intern']]['rows'] = $item['rows'];
					$ausgabe[$item['intern']]['flags'] = $item['flags'];
				}
				else
				{
					// Datensatz ist nicht vorkonfiguriert
					$ausgabe[$item['intern']] = array
					(
						'name'    => $item['intern'],
						'color'   => '',
						'rows'    => $item['rows'],
						'flags'   => $item['flags'],
						'defined' => false,
					);
				}
			}
		}
		
		$content = '';
		$row = 0;
		$content .= '<div>';
		$content .= '<span style="display:inline-block; width:15%; font-style:italic;">Name</span>';
		$content .= '<span style="display:inline-block; width:15%; font-style:italic; margin-right:10px;">Farbe</span>';
		$content .= '<span style="display:inline-block; width:32%; font-style:italic; margin-right:5px;">Zeilennummern</span>';
		$content .= '<span style="display:inline-block; width:32%; font-style:italic;">Länder</span>';
		$content .= '</div>';
		foreach($ausgabe as $key => $value)
		{
			if($key)
			{
				$content .= '<div>';
				$content .= '<input type="hidden" name="'.$this->strName.'['.$row.'][intern]" value="'.$key.'">';
				if($ausgabe[$key]['defined']) $content .= '<span style="display:inline-block; width:15%; font-weight:bold;">'.$ausgabe[$key]['name'].'</span>';
				else $content .= '<span style="display:inline-block; width:15%; font-weight:bold; color:red;">'.$ausgabe[$key]['name'].'</span>';
				$content .= '<span style="display:inline-block; width:15%; margin-right:10px; background-color:'.$ausgabe[$key]['color'].'">&nbsp;</span>';
				$content .= '<input type="text" name="'.$this->strName.'['.$row.'][rows]" id="ctrl_'.$this->strName.'_'.$key.'" class="tl_text" style="width:32%; margin-right:5px;" value="'.$ausgabe[$key]['rows'].'" onfocus="Backend.getScrollOffset()">';
				$content .= '<input type="text" name="'.$this->strName.'['.$row.'][flags]" id="ctrl_'.$this->strName.'_'.$key.'" class="tl_text" style="width:32%" value="'.$ausgabe[$key]['flags'].'" onfocus="Backend.getScrollOffset()">';
				$content .= '</div>';
				$row++;
			}
		}
		return $content;

	}
}
