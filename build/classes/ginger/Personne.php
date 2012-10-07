<?php



/**
 * Skeleton subclass for representing a row from the 'personne' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ginger
 */
class Personne extends BasePersonne
{
	
	public function isCotisant()
	{
		$crit = new Criteria();
		$crit->add(CotisationPeer::DEBUT, Criteria::CURRENT_DATE, Criteria::LESS_THAN);
		$crit->add(CotisationPeer::FIN, Criteria::CURRENT_DATE, Criteria::GREATER_THAN);
		
		return !$this->getCotisations($crit)->isEmpty();
	}
	
	public function updateFromAccounts(){
		if($this->getLogin()){
			$personneData = AccountsApi::getUserInfo($this->getLogin());
		}
		if(!$personneData && $this->getBadgeUid()){
			$personneData = AccountsApi::cardLookup($this->getBadgeUid());
		}
		
		if($personneData){
			$this->setLogin($personneData->username);
			$this->setPrenom(ucfirst(strtolower($personneData->firstName)));
			$this->setNom(strtoupper($personneData->lastName));
			$this->setMail($personneData->mail);
			switch($personneData->profile){
				case "ETU UTC":
					$this->setType("etu");
					break;
				case "ETU ESCOM":
					$this->setType("escom");
					break;
				case "PERSONNEL":
					$this->setType("pers");
					break;
				case "PERSONNEL ESCOM": // Purement théorique pour l'instant
				$this->setType("escompers");
					break;
			}
			$this->setBadgeUid($personneData->cardSerialNumber);
			$this->setExpirationBadge($personneData->cardEndDate/1000);
			$this->setIsAdulte($personneData->legalAge);
			return true;
		}
		return false;
	}
}
