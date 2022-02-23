<?php
// Tabellen umstrukturieren

/**
 * Runonce Job
 */
class runonceJob extends \Backend
{

	public function __construct()
	{
		parent::__construct();
		$this->import('Database');
	}

	/**
	 * Run job
	 */
	public function run()
	{
		// Alle Datensätze laden und modifizieren
		// In der Tabelle tl_content werden die Felder chesstable_aufsteiger, chesstable_absteiger und chesstable_markieren
		// ausgelesen und die Werte in das Feld chesstable_markierungen übertragen
		$result = $this->Database->prepare("SELECT * FROM tl_content")
		                         ->execute();

		if($result->numRows)
		{

			while($result->next())
			{

				$markierungen = array
				(
					array('intern' => 'up', 'rows' => $result->chesstable_aufsteiger),
					array('intern' => 'down', 'rows' => $result->chesstable_absteiger),
					array('intern' => 'high', 'rows' => $result->chesstable_markieren)
				);
				
				// Datensatz aktualisieren
				$set = array
				(
					'chesstable_markierungen' => serialize($markierungen)
				);
				$this->Database->prepare("UPDATE tl_content %s WHERE id = ?")
				               ->set($set)
				               ->execute($result->id);
			}
		}
	}
}

// Run once
$objRunonceJob = new runonceJob();
$objRunonceJob->run();
