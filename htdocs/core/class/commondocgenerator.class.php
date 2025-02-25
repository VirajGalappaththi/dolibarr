<?php
/* Copyright (C) 2003-2005	Rodolphe Quiedeville    <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2010	Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2004		Eric Seigne             <eric.seigne@ryxeo.com>
 * Copyright (C) 2005-2012	Regis Houssin           <regis.houssin@inodbox.com>
 * Copyright (C) 2015       Marcos García           <marcosgdf@gmail.com>
 * Copyright (C) 2016       Charlie Benke           <charlie@patas-monkey.com>
 * Copyright (C) 2018       Frédéric France         <frederic.france@netlogic.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 * or see https://www.gnu.org/
 */

/**
 *	    \file       htdocs/core/class/commondocgenerator.class.php
 *		\ingroup    core
 *		\brief      File of parent class for documents generators
 */


/**
 *	Parent class for documents generators
 */
abstract class CommonDocGenerator
{
	/**
	 * @var string Error code (or message)
	 */
	public $error='';

    /**
     * @var string[]    Array of error strings
     */
    public $errors = array();

	/**
     * @var DoliDB Database handler.
     */
	protected $db;


	/**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	*/
    public function __construct($db)
    {
        $this->db = $db;
    }


    // phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
    /**
     * Define array with couple substitution key => substitution value
     *
     * @param   User		$user           User
     * @param   Translate	$outputlangs    Language object for output
     * @return	array						Array of substitution key->code
     */
    public function get_substitutionarray_user($user, $outputlangs)
    {
        // phpcs:enable
        global $conf;

        $logotouse=$conf->user->dir_output.'/'.get_exdir($user->id, 2, 0, 1, $user, 'user').'/'.$user->photo;

        return array(
            'myuser_lastname'=>$user->lastname,
            'myuser_firstname'=>$user->firstname,
            'myuser_fullname'=>$user->getFullName($outputlangs, 1),
            'myuser_login'=>$user->login,
            'myuser_phone'=>$user->office_phone,
       		'myuser_address'=>$user->address,
       		'myuser_zip'=>$user->zip,
       		'myuser_town'=>$user->town,
       		'myuser_country'=>$user->country,
        	'myuser_country_code'=>$user->country_code,
       		'myuser_state'=>$user->state,
        	'myuser_state_code'=>$user->state_code,
        	'myuser_fax'=>$user->office_fax,
            'myuser_mobile'=>$user->user_mobile,
            'myuser_email'=>$user->email,
        	'myuser_logo'=>$logotouse,
        	'myuser_job'=>$user->job,
            'myuser_web'=>''	// url not exist in $user object
        );
    }


    // phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
    /**
     * Define array with couple substitution key => substitution value
     *
     * @param   Societe		$mysoc			Object thirdparty
     * @param   Translate	$outputlangs    Language object for output
     * @return	array						Array of substitution key->code
     */
    public function get_substitutionarray_mysoc($mysoc, $outputlangs)
    {
        // phpcs:enable
        global $conf;

        if (empty($mysoc->forme_juridique) && ! empty($mysoc->forme_juridique_code))
        {
            $mysoc->forme_juridique=getFormeJuridiqueLabel($mysoc->forme_juridique_code);
        }
        if (empty($mysoc->country) && ! empty($mysoc->country_code))
        {
        	$mysoc->country=$outputlangs->transnoentitiesnoconv("Country".$mysoc->country_code);
        }
        if (empty($mysoc->state) && ! empty($mysoc->state_code))
        {
        	$mysoc->state=getState($mysoc->state_code, 0);
        }

        $logotouse=$conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_small;

        return array(
            'mycompany_logo'=>$logotouse,
            'mycompany_name'=>$mysoc->name,
            'mycompany_email'=>$mysoc->email,
            'mycompany_phone'=>$mysoc->phone,
            'mycompany_fax'=>$mysoc->fax,
            'mycompany_address'=>$mysoc->address,
            'mycompany_zip'=>$mysoc->zip,
            'mycompany_town'=>$mysoc->town,
            'mycompany_country'=>$mysoc->country,
            'mycompany_country_code'=>$mysoc->country_code,
            'mycompany_state'=>$mysoc->state,
            'mycompany_state_code'=>$mysoc->state_code,
        	'mycompany_web'=>$mysoc->url,
            'mycompany_juridicalstatus'=>$mysoc->forme_juridique,
            'mycompany_managers'=>$mysoc->managers,
            'mycompany_capital'=>$mysoc->capital,
            'mycompany_barcode'=>$mysoc->barcode,
            'mycompany_idprof1'=>$mysoc->idprof1,
            'mycompany_idprof2'=>$mysoc->idprof2,
            'mycompany_idprof3'=>$mysoc->idprof3,
            'mycompany_idprof4'=>$mysoc->idprof4,
            'mycompany_idprof5'=>$mysoc->idprof5,
            'mycompany_idprof6'=>$mysoc->idprof6,
        	'mycompany_vatnumber'=>$mysoc->tva_intra,
			'mycompany_object'=>$mysoc->object,
            'mycompany_note_private'=>$mysoc->note_private,
            //'mycompany_note_public'=>$mysoc->note_public,        // Only private not exists for "mysoc" but both for thirdparties
        );
    }


    // phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
    /**
     * Define array with couple substitution key => substitution value
     *
     * @param	Societe		$object			Object
     * @param   Translate	$outputlangs    Language object for output
     * @return	array						Array of substitution key->code
     */
    public function get_substitutionarray_thirdparty($object, $outputlangs)
    {
        // phpcs:enable
        global $conf;

        if (empty($object->country) && ! empty($object->country_code))
        {
        	$object->country=$outputlangs->transnoentitiesnoconv("Country".$object->country_code);
        }
        if (empty($object->state) && ! empty($object->state_code))
        {
        	$object->state=getState($object->state_code, 0);
        }

        $array_thirdparty = array(
            'company_name'=>$object->name,
	        'company_name_alias' => $object->name_alias,
            'company_email'=>$object->email,
            'company_phone'=>$object->phone,
            'company_fax'=>$object->fax,
            'company_address'=>$object->address,
            'company_zip'=>$object->zip,
            'company_town'=>$object->town,
            'company_country'=>$object->country,
        	'company_country_code'=>$object->country_code,
            'company_state'=>$object->state,
        	'company_state_code'=>$object->state_code,
        	'company_web'=>$object->url,
            'company_barcode'=>$object->barcode,
            'company_vatnumber'=>$object->tva_intra,
            'company_customercode'=>$object->code_client,
            'company_suppliercode'=>$object->code_fournisseur,
            'company_customeraccountancycode'=>$object->code_compta,
            'company_supplieraccountancycode'=>$object->code_compta_fournisseur,
            'company_juridicalstatus'=>$object->forme_juridique,
            'company_outstanding_limit'=>$object->outstanding_limit,
            'company_capital'=>$object->capital,
            'company_idprof1'=>$object->idprof1,
            'company_idprof2'=>$object->idprof2,
            'company_idprof3'=>$object->idprof3,
            'company_idprof4'=>$object->idprof4,
            'company_idprof5'=>$object->idprof5,
            'company_idprof6'=>$object->idprof6,
            'company_note_public'=>$object->note_public,
            'company_note_private'=>$object->note_private,
            'company_default_bank_iban'=>$object->bank_account->iban,
            'company_default_bank_bic'=>$object->bank_account->bic
        );

        // Retrieve extrafields
        if(is_array($object->array_options) && count($object->array_options))
        {
        	require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
        	$extrafields = new ExtraFields($this->db);
        	$extrafields->fetch_name_optionals_label($object->table_element, true);
        	$object->fetch_optionals();

        	foreach($extrafields->attributes[$object->table_element]['label'] as $key=>$label)
        	{
        		if($extrafields->attributes[$object->table_element]['type'][$key] == 'price')
        		{
        			$object->array_options['options_'.$key] = price($object->array_options['options_'.$key], 0, $outputlangs, 0, 0, -1, $conf->currency);
        		}
        		elseif($extrafields->attributes[$object->table_element]['type'][$key] == 'select' || $extrafields->attributes[$object->table_element]['type'][$key] == 'checkbox')
        		{
        			$object->array_options['options_'.$key] = $extrafields->attributes[$object->table_element]['param'][$key]['options'][$object->array_options['options_'.$key]];
        		}
        		$array_thirdparty = array_merge($array_thirdparty, array ('company_options_'.$key => $object->array_options ['options_' . $key]));
			}
		}
		return $array_thirdparty;
	}

    // phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Define array with couple substitution key => substitution value
	 *
	 * @param	Contact 	$object        	contact
	 * @param	Translate 	$outputlangs   	object for output
	 * @param   array		$array_key	    Name of the key for return array
	 * @return	array 						Array of substitution key->code
	 */
    public function get_substitutionarray_contact($object, $outputlangs, $array_key = 'object')
    {
        // phpcs:enable
		global $conf;

		if(empty($object->country) && ! empty($object->country_code))
		{
			$object->country = $outputlangs->transnoentitiesnoconv("Country" . $object->country_code);
		}
		if(empty($object->state) && ! empty($object->state_code))
		{
			$object->state = getState($object->state_code, 0);
		}

		$array_contact = array (
		    $array_key . '_fullname' => $object->getFullName($outputlangs, 1),
            $array_key . '_lastname' => $object->lastname,
            $array_key . '_firstname' => $object->firstname,
            $array_key . '_address' => $object->address,
            $array_key . '_zip' => $object->zip,
            $array_key . '_town' => $object->town,
            $array_key . '_state_id' => $object->state_id,
            $array_key . '_state_code' => $object->state_code,
            $array_key . '_state' => $object->state,
            $array_key . '_country_id' => $object->country_id,
            $array_key . '_country_code' => $object->country_code,
            $array_key . '_country' => $object->country,
            $array_key . '_poste' => $object->poste,
            $array_key . '_socid' => $object->socid,
            $array_key . '_statut' => $object->statut,
            $array_key . '_code' => $object->code,
            $array_key . '_email' => $object->email,
            $array_key . '_jabberid' => $object->jabberid,
            $array_key . '_phone_pro' => $object->phone_pro,
            $array_key . '_phone_perso' => $object->phone_perso,
            $array_key . '_phone_mobile' => $object->phone_mobile,
            $array_key . '_fax' => $object->fax,
            $array_key . '_birthday' => $object->birthday,
            $array_key . '_default_lang' => $object->default_lang,
            $array_key . '_note_public' => $object->note_public,
            $array_key . '_note_private' => $object->note_private
		);

		// Retrieve extrafields
		require_once DOL_DOCUMENT_ROOT . '/core/class/extrafields.class.php';
		$extrafields = new ExtraFields($this->db);
		$extrafields->fetch_name_optionals_label($object->table_element, true);
		$object->fetch_optionals();

		foreach($extrafields->attributes[$object->table_element]['label'] as $key => $label)
		{
			if ($extrafields->attributes[$object->table_element]['type'][$key] == 'price')
			{
				$object->array_options['options_' . $key] = price($object->array_options ['options_' . $key], 0, $outputlangs, 0, 0, - 1, $conf->currency);
			}
			elseif($extrafields->attributes[$object->table_element]['type'][$key] == 'select' || $extrafields->attributes[$object->table_element]['type'][$key] == 'checkbox')
			{
				$object->array_options['options_' . $key] = $extrafields->attributes[$object->table_element]['param'][$key]['options'][$object->array_options['options_' . $key]];
			}
			$array_contact = array_merge($array_contact, array($array_key.'_options_' . $key => $object->array_options['options_'. $key]));
		}
		return $array_contact;
	}


    // phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
    /**
     * Define array with couple substitution key => substitution value
     *
     * @param   Translate	$outputlangs    Language object for output
     * @return	array						Array of substitution key->code
     */
    public function get_substitutionarray_other($outputlangs)
    {
        // phpcs:enable
    	global $conf;

    	$now=dol_now('gmt');	// gmt
    	$array_other = array(
    	    // Date in default language
    	    'current_date'=>dol_print_date($now, 'day', 'tzuser'),
    	    'current_datehour'=>dol_print_date($now, 'dayhour', 'tzuser'),
   			'current_server_date'=>dol_print_date($now, 'day', 'tzserver'),
   			'current_server_datehour'=>dol_print_date($now, 'dayhour', 'tzserver'),
    	    // Date in requested output language
    	    'current_date_locale'=>dol_print_date($now, 'day', 'tzuser', $outputlangs),
   			'current_datehour_locale'=>dol_print_date($now, 'dayhour', 'tzuser', $outputlangs),
   			'current_server_date_locale'=>dol_print_date($now, 'day', 'tzserver', $outputlangs),
   			'current_server_datehour_locale'=>dol_print_date($now, 'dayhour', 'tzserver', $outputlangs),
    	);


    	foreach($conf->global as $key => $val)
    	{
    		if (preg_match('/(_pass|password|secret|_key|key$)/i', $key)) $newval = '*****forbidden*****';
    		else $newval = $val;
    		$array_other['__['.$key.']__'] = $newval;
    	}

    	return $array_other;
    }


    // phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Define array with couple substitution key => substitution value
	 *
	 * @param   Object			$object             Main object to use as data source
	 * @param   Translate		$outputlangs        Lang object to use for output
     * @param   string		    $array_key	        Name of the key for return array
	 * @return	array								Array of substitution
	 */
	public function get_substitutionarray_object($object, $outputlangs, $array_key = 'object')
	{
        // phpcs:enable
		global $conf;

		$sumpayed=$sumdeposit=$sumcreditnote='';
		$already_payed_all=0;
		$remain_to_pay=0;
		if ($object->element == 'facture')
		{
			$invoice_source=new Facture($this->db);
			if ($object->fk_facture_source > 0)
			{
				$invoice_source->fetch($object->fk_facture_source);
			}
			$sumpayed = $object->getSommePaiement();
			$sumdeposit = $object->getSumDepositsUsed();
			$sumcreditnote = $object->getSumCreditNotesUsed();
			$already_payed_all=$sumpayed + $sumdeposit + $sumcreditnote;
			$remain_to_pay=$sumpayed - $sumdeposit - $sumcreditnote;
		}

		$date = ($object->element == 'contrat' ? $object->date_contrat : $object->date);

		$resarray=array(
		$array_key.'_id'=>$object->id,
		$array_key.'_ref'=>$object->ref,
		$array_key.'_ref_ext'=>$object->ref_ext,
		$array_key.'_ref_customer'=>(! empty($object->ref_client) ? $object->ref_client : (empty($object->ref_customer) ? '' : $object->ref_customer)),
		$array_key.'_ref_supplier'=>(! empty($object->ref_fournisseur) ? $object->ref_fournisseur : (empty($object->ref_supplier) ? '' : $object->ref_supplier)),
		$array_key.'_source_invoice_ref'=>$invoice_source->ref,
		// Dates
        $array_key.'_hour'=>dol_print_date($date, 'hour'),
		$array_key.'_date'=>dol_print_date($date, 'day'),
		$array_key.'_date_rfc'=>dol_print_date($date, 'dayrfc'),
		$array_key.'_date_limit'=>(! empty($object->date_lim_reglement)?dol_print_date($object->date_lim_reglement, 'day'):''),
	    $array_key.'_date_end'=>(! empty($object->fin_validite)?dol_print_date($object->fin_validite, 'day'):''),
		$array_key.'_date_creation'=>dol_print_date($object->date_creation, 'day'),
		$array_key.'_date_modification'=>(! empty($object->date_modification)?dol_print_date($object->date_modification, 'day'):''),
		$array_key.'_date_validation'=>(! empty($object->date_validation)?dol_print_date($object->date_validation, 'dayhour'):''),
		$array_key.'_date_delivery_planed'=>(! empty($object->date_livraison)?dol_print_date($object->date_livraison, 'day'):''),
		$array_key.'_date_close'=>(! empty($object->date_cloture)?dol_print_date($object->date_cloture, 'dayhour'):''),

		$array_key.'_payment_mode_code'=>$object->mode_reglement_code,
		$array_key.'_payment_mode'=>($outputlangs->transnoentitiesnoconv('PaymentType'.$object->mode_reglement_code)!='PaymentType'.$object->mode_reglement_code?$outputlangs->transnoentitiesnoconv('PaymentType'.$object->mode_reglement_code):$object->mode_reglement),
		$array_key.'_payment_term_code'=>$object->cond_reglement_code,
		$array_key.'_payment_term'=>($outputlangs->transnoentitiesnoconv('PaymentCondition'.$object->cond_reglement_code)!='PaymentCondition'.$object->cond_reglement_code?$outputlangs->transnoentitiesnoconv('PaymentCondition'.$object->cond_reglement_code):($object->cond_reglement_doc?$object->cond_reglement_doc:$object->cond_reglement)),

		$array_key.'_total_ht_locale'=>price($object->total_ht, 0, $outputlangs),
		$array_key.'_total_vat_locale'=>(! empty($object->total_vat)?price($object->total_vat, 0, $outputlangs):price($object->total_tva, 0, $outputlangs)),
		$array_key.'_total_localtax1_locale'=>price($object->total_localtax1, 0, $outputlangs),
		$array_key.'_total_localtax2_locale'=>price($object->total_localtax2, 0, $outputlangs),
		$array_key.'_total_ttc_locale'=>price($object->total_ttc, 0, $outputlangs),

		$array_key.'_total_ht'=>price2num($object->total_ht),
		$array_key.'_total_vat'=>(! empty($object->total_vat)?price2num($object->total_vat):price2num($object->total_tva)),
		$array_key.'_total_localtax1'=>price2num($object->total_localtax1),
		$array_key.'_total_localtax2'=>price2num($object->total_localtax2),
		$array_key.'_total_ttc'=>price2num($object->total_ttc),

		$array_key.'_multicurrency_code' => price2num($object->multicurrency_code),
		$array_key.'_multicurrency_tx' => price2num($object->multicurrency_tx),
	    $array_key.'_multicurrency_total_ht' => price2num($object->multicurrency_total_ht),
	    $array_key.'_multicurrency_total_tva' => price2num($object->multicurrency_total_tva),
		$array_key.'_multicurrency_total_ttc' => price2num($object->multicurrency_total_ttc),
		$array_key.'_multicurrency_total_ht_locale' => price($object->multicurrency_total_ht, 0, $outputlangs),
		$array_key.'_multicurrency_total_tva_locale' => price($object->multicurrency_total_tva, 0, $outputlangs),
		$array_key.'_multicurrency_total_ttc_locale' => price($object->multicurrency_total_ttc, 0, $outputlangs),

		$array_key.'_note_private'=>$object->note,
		$array_key.'_note_public'=>$object->note_public,
		$array_key.'_note'=>$object->note_public,			// For backward compatibility

		// Payments
		$array_key.'_already_payed_locale'=>price($sumpayed, 0, $outputlangs),
		$array_key.'_already_payed'=>price2num($sumpayed),
		$array_key.'_already_deposit_locale'=>price($sumdeposit, 0, $outputlangs),
		$array_key.'_already_deposit'=>price2num($sumdeposit),
		$array_key.'_already_creditnote_locale'=>price($sumcreditnote, 0, $outputlangs),
		$array_key.'_already_creditnote'=>price2num($sumcreditnote),

		$array_key.'_already_payed_all_locale'=>price(price2num($already_payed_all, 'MT'), 0, $outputlangs),
		$array_key.'_already_payed_all'=> price2num($already_payed_all, 'MT'),

		// Remain to pay with all know information (except open direct debit requests)
		$array_key.'_remain_to_pay_locale'=>price(price2num($object->total_ttc - $remain_to_pay, 'MT'), 0, $outputlangs),
		$array_key.'_remain_to_pay'=>price2num($object->total_ttc - $remain_to_pay, 'MT')
		);

		if (method_exists($object, 'getTotalDiscount')) {
			$resarray[$array_key.'_total_discount_ht_locale'] = price($object->getTotalDiscount(), 0, $outputlangs);
			$resarray[$array_key.'_total_discount_ht'] = price2num($object->getTotalDiscount());
		} else {
			$resarray[$array_key.'_total_discount_ht_locale'] = '';
			$resarray[$array_key.'_total_discount_ht'] = '';
		}

		// Fetch project information if there is a project assigned to this object
		if ($object->element != "project" && ! empty($object->fk_project) && $object->fk_project > 0)
		{
			if (! is_object($object->project))
			{
				$object->fetch_projet();
			}

			$resarray[$array_key.'_project_ref'] = $object->project->ref;
			$resarray[$array_key.'_project_title'] = $object->project->title;
			$resarray[$array_key.'_project_description'] = $object->project->description;
			$resarray[$array_key.'_project_date_start'] = dol_print_date($object->project->date_start, 'day');
			$resarray[$array_key.'_project_date_end'] = dol_print_date($object->project->date_end, 'day');
		}

		// Add vat by rates
		if (is_array($object->lines) && count($object->lines)>0)
		{
			$totalUp = 0;
			foreach ($object->lines as $line)
			{
			    // $line->tva_tx format depends on database field accuraty, no reliable. This is kept for backward compatibility
				if (empty($resarray[$array_key.'_total_vat_'.$line->tva_tx])) $resarray[$array_key.'_total_vat_'.$line->tva_tx]=0;
				$resarray[$array_key.'_total_vat_'.$line->tva_tx]+=$line->total_tva;
				$resarray[$array_key.'_total_vat_locale_'.$line->tva_tx]=price($resarray[$array_key.'_total_vat_'.$line->tva_tx]);
			    // $vatformated is vat without not expected chars (so 20, or 8.5 or 5.99 for example)
				$vatformated=vatrate($line->tva_tx);
				if (empty($resarray[$array_key.'_total_vat_'.$vatformated])) $resarray[$array_key.'_total_vat_'.$vatformated]=0;
				$resarray[$array_key.'_total_vat_'.$vatformated]+=$line->total_tva;
				$resarray[$array_key.'_total_vat_locale_'.$vatformated]=price($resarray[$array_key.'_total_vat_'.$vatformated]);

				$totalUp += $line->subprice * $line->qty;
			}

			// @GS: Calculate total up and total discount percentage
			// Note that this added fields correspond to nothing in Dolibarr (Dolibarr manage discount on lines not globally)
			$resarray['object_total_up'] = $totalUp;
			$resarray['object_total_up_locale'] = price($resarray['object_total_up'], 0, $outputlangs);
			if (method_exists($object, 'getTotalDiscount')) {
				$totalDiscount=$object->getTotalDiscount();
			} else {
				$totalDiscount=0;
			}
			if (!empty($totalUp) && !empty($totalDiscount)) {
				$resarray['object_total_discount'] = round(100 / $totalUp * $totalDiscount, 2);
				$resarray['object_total_discount_locale'] = price($resarray['object_total_discount'], 0, $outputlangs);
			} else {
				$resarray['object_total_discount']='';
				$resarray['object_total_discount_locale']='';
			}
		}

		// Retrieve extrafields
		if (is_array($object->array_options) && count($object->array_options))
		{
			$extrafieldkey=$object->element;

			require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
			$extrafields = new ExtraFields($this->db);
			$extrafields->fetch_name_optionals_label($extrafieldkey, true);
			$object->fetch_optionals();

			$resarray = $this->fill_substitutionarray_with_extrafields($object, $resarray, $extrafields, $array_key, $outputlangs);
		}

		return $resarray;
	}

    // phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *	Define array with couple substitution key => substitution value
	 *
	 *	@param  Object			$line				Object line
	 *	@param  Translate		$outputlangs        Lang object to use for output
	 *  @return	array								Return a substitution array
	 */
	public function get_substitutionarray_lines($line, $outputlangs)
	{
        // phpcs:enable
		global $conf;

		$resarray= array(
			'line_fulldesc'=>doc_getlinedesc($line, $outputlangs),
			'line_product_ref'=>$line->product_ref,
			'line_product_ref_fourn'=>$line->ref_fourn, // for supplier doc lines
			'line_product_label'=>$line->product_label,
			'line_product_type'=>$line->product_type,
			'line_desc'=>$line->desc,
			'line_vatrate'=>vatrate($line->tva_tx, true, $line->info_bits),
		    'line_localtax1_rate'=>vatrate($line->localtax1_tx),
		    'line_localtax2_rate'=>vatrate($line->localtax1_tx),
		    'line_up'=>price2num($line->subprice),
			'line_up_locale'=>price($line->subprice, 0, $outputlangs),
			'line_total_up'=>price2num($line->subprice * $line->qty),
			'line_total_up_locale'=>price($line->subprice * $line->qty, 0, $outputlangs),
			'line_qty'=>$line->qty,
			'line_discount_percent'=>($line->remise_percent?$line->remise_percent.'%':''),
			'line_price_ht'=>price2num($line->total_ht),
			'line_price_ttc'=>price2num($line->total_ttc),
			'line_price_vat'=>price2num($line->total_tva),
			'line_price_ht_locale'=>price($line->total_ht, 0, $outputlangs),
			'line_price_ttc_locale'=>price($line->total_ttc, 0, $outputlangs),
			'line_price_vat_locale'=>price($line->total_tva, 0, $outputlangs),
		    // Dates
			'line_date_start'=>dol_print_date($line->date_start, 'day', 'tzuser'),
			'line_date_start_locale'=>dol_print_date($line->date_start, 'day', 'tzuser', $outputlangs),
		    'line_date_start_rfc'=>dol_print_date($line->date_start, 'dayrfc', 'tzuser'),
		    'line_date_end'=>dol_print_date($line->date_end, 'day', 'tzuser'),
		    'line_date_end_locale'=>dol_print_date($line->date_end, 'day', 'tzuser', $outputlangs),
		    'line_date_end_rfc'=>dol_print_date($line->date_end, 'dayrfc', 'tzuser'),

		    'line_multicurrency_code' => price2num($line->multicurrency_code),
		    'line_multicurrency_subprice' => price2num($line->multicurrency_subprice),
		    'line_multicurrency_total_ht' => price2num($line->multicurrency_total_ht),
		    'line_multicurrency_total_tva' => price2num($line->multicurrency_total_tva),
		    'line_multicurrency_total_ttc' => price2num($line->multicurrency_total_ttc),
		    'line_multicurrency_subprice_locale' => price($line->multicurrency_subprice, 0, $outputlangs),
		    'line_multicurrency_total_ht_locale' => price($line->multicurrency_total_ht, 0, $outputlangs),
		    'line_multicurrency_total_tva_locale' => price($line->multicurrency_total_tva, 0, $outputlangs),
		    'line_multicurrency_total_ttc_locale' => price($line->multicurrency_total_ttc, 0, $outputlangs),
		);

        // Units
		if ($conf->global->PRODUCT_USE_UNITS)
		{
		      $resarray['line_unit']=$outputlangs->trans($line->getLabelOfUnit('long'));
		      $resarray['line_unit_short']=$outputlangs->trans($line->getLabelOfUnit('short'));
		}

		// Retrieve extrafields
		$extrafieldkey=$line->element;
		$array_key="line";
		require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		$extrafields = new ExtraFields($this->db);
		$extrafields->fetch_name_optionals_label($extrafieldkey, true);
		$line->fetch_optionals();

		$resarray = $this->fill_substitutionarray_with_extrafields($line, $resarray, $extrafields, $array_key, $outputlangs);

		// Load product data optional fields to the line -> enables to use "line_options_{extrafield}"
		if (isset($line->fk_product) && $line->fk_product > 0)
		{
			$tmpproduct = new Product($this->db);
			$result = $tmpproduct->fetch($line->fk_product);
			foreach($tmpproduct->array_options as $key=>$label)
				$resarray["line_product_".$key] = $label;
		}

		return $resarray;
	}

    // phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
    /**
     * Define array with couple substitution key => substitution value
     *
     * @param   Expedition		$object             Main object to use as data source
     * @param   Translate		$outputlangs        Lang object to use for output
     * @param   array			$array_key	        Name of the key for return array
     * @return	array								Array of substitution
     */
    public function get_substitutionarray_shipment($object, $outputlangs, $array_key = 'object')
    {
        // phpcs:enable
    	global $conf;
		dol_include_once('/core/lib/product.lib.php');
		$object->list_delivery_methods($object->shipping_method_id);
		$calculatedVolume=($object->trueWidth * $object->trueHeight * $object->trueDepth);

    	$array_shipment=array(
	    	$array_key.'_id'=>$object->id,
	    	$array_key.'_ref'=>$object->ref,
	    	$array_key.'_ref_ext'=>$object->ref_ext,
	    	$array_key.'_ref_customer'=>$object->ref_customer,
	    	$array_key.'_date_delivery'=>dol_print_date($object->date_delivery, 'day'),
	    	$array_key.'_hour_delivery'=>dol_print_date($object->date_delivery, 'hour'),
	    	$array_key.'_date_creation'=>dol_print_date($object->date_creation, 'day'),
	    	$array_key.'_total_ht'=>price($object->total_ht),
	    	$array_key.'_total_vat'=>price($object->total_tva),
	    	$array_key.'_total_ttc'=>price($object->total_ttc),
	    	$array_key.'_total_discount_ht' => price($object->getTotalDiscount()),
	    	$array_key.'_note_private'=>$object->note_private,
	    	$array_key.'_note'=>$object->note_public,
	    	$array_key.'_tracking_number'=>$object->tracking_number,
	    	$array_key.'_tracking_url'=>$object->tracking_url,
	    	$array_key.'_shipping_method'=>$object->listmeths[0]['libelle'],
    		$array_key.'_weight'=>$object->trueWeight.' '.measuringUnitString(0, 'weight', $object->weight_units),
    		$array_key.'_width'=>$object->trueWidth.' '.measuringUnitString(0, 'size', $object->width_units),
    		$array_key.'_height'=>$object->trueHeight.' '.measuringUnitString(0, 'size', $object->height_units),
    		$array_key.'_depth'=>$object->trueDepth.' '.measuringUnitString(0, 'size', $object->depth_units),
	    	$array_key.'_size'=>$calculatedVolume.' '.measuringUnitString(0, 'volume'),
    	);

    	// Add vat by rates
    	foreach ($object->lines as $line)
    	{
    		if (empty($array_shipment[$array_key.'_total_vat_'.$line->tva_tx])) $array_shipment[$array_key.'_total_vat_'.$line->tva_tx]=0;
    		$array_shipment[$array_key.'_total_vat_'.$line->tva_tx]+=$line->total_tva;
    	}

    	// Retrieve extrafields
    	if (is_array($object->array_options) && count($object->array_options))
    	{
    		require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
    		$extrafields = new ExtraFields($this->db);
    		$extrafields->fetch_name_optionals_label('expedition', true);
    		$object->fetch_optionals();

    		$array_shipment = $this->fill_substitutionarray_with_extrafields($object, $array_shipment, $extrafields, $array_key, $outputlangs);
    	}

    	return $array_shipment;
    }


    // phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
    /**
     *  Define array with couple substitution key => substitution value
     *
     *	@param  ExpeditionLigne	$line				Object line
     *	@param  Translate		$outputlangs        Lang object to use for output
     *	@return	array								Substitution array
     */
    public function get_substitutionarray_shipment_lines($line, $outputlangs)
    {
        // phpcs:enable
        global $conf;
        dol_include_once('/core/lib/product.lib.php');

        $resarray = array(
	    	'line_fulldesc'=>doc_getlinedesc($line, $outputlangs),
	    	'line_product_ref'=>$line->product_ref,
	    	'line_product_label'=>$line->product_label,
	    	'line_desc'=>$line->desc,
	    	'line_vatrate'=>vatrate($line->tva_tx, true, $line->info_bits),
	    	'line_up'=>price($line->subprice),
            'line_total_up'=>price($line->subprice * $line->qty),
	    	'line_qty'=>$line->qty,
	    	'line_qty_shipped'=>$line->qty_shipped,
	    	'line_qty_asked'=>$line->qty_asked,
	    	'line_discount_percent'=>($line->remise_percent?$line->remise_percent.'%':''),
	    	'line_price_ht'=>price($line->total_ht),
	    	'line_price_ttc'=>price($line->total_ttc),
	    	'line_price_vat'=>price($line->total_tva),
        	'line_weight'=>empty($line->weight) ? '' : $line->weight*$line->qty_shipped.' '.measuringUnitString(0, 'weight', $line->weight_units),
        	'line_length'=>empty($line->length) ? '' : $line->length*$line->qty_shipped.' '.measuringUnitString(0, 'size', $line->length_units),
        	'line_surface'=>empty($line->surface) ? '' : $line->surface*$line->qty_shipped.' '.measuringUnitString(0, 'surface', $line->surface_units),
        	'line_volume'=>empty($line->volume) ? '' : $line->volume*$line->qty_shipped.' '.measuringUnitString(0, 'volume', $line->volume_units),
    	);

        // Retrieve extrafields
        $extrafieldkey = $line->element;
        $array_key = "line";
        require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
        $extrafields = new ExtraFields($this->db);
        $extrafields->fetch_name_optionals_label($extrafieldkey, true);
        $line->fetch_optionals();

        $resarray = $this->fill_substitutionarray_with_extrafields($line, $resarray, $extrafields, $array_key, $outputlangs);

        return $resarray;
    }


    // phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
    /**
     * Define array with couple substitution key => substitution value
     *
     * @param   Object		$object    		Dolibarr Object
     * @param   Translate	$outputlangs    Language object for output
     * @param   boolean		$recursive    	Want to fetch child array or child object
     * @return	array						Array of substitution key->code
     */
    public function get_substitutionarray_each_var_object(&$object, $outputlangs, $recursive = true)
    {
        // phpcs:enable
        $array_other = array();
        if (!empty($object)) {
            foreach($object as $key => $value) {
                if (!empty($value)) {
                    if (!is_array($value) && !is_object($value)) {
                        $array_other['object_'.$key] = $value;
                    }
                    if (is_array($value) && $recursive) {
                        $array_other['object_'.$key] = $this->get_substitutionarray_each_var_object($value, $outputlangs, false);
                    }
                }
            }
        }
        return $array_other;
    }


    // phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
    /**
     *	Fill array with couple extrafield key => extrafield value
     *
     *	@param  Object			$object				Object with extrafields (must have $object->array_options filled)
     *	@param  array			$array_to_fill      Substitution array
     *  @param  Extrafields		$extrafields        Extrafields object
     *  @param  string			$array_key	        Prefix for name of the keys into returned array
     *  @param  Translate		$outputlangs        Lang object to use for output
     *	@return	array								Substitution array
     */
    public function fill_substitutionarray_with_extrafields($object, $array_to_fill, $extrafields, $array_key, $outputlangs)
    {
        // phpcs:enable
		global $conf;
		foreach($extrafields->attributes[$object->table_element]['label'] as $key=>$label)
		{
			if($extrafields->attributes[$object->table_element]['type'][$key] == 'price')
			{
				$object->array_options['options_'.$key] = price2num($object->array_options['options_'.$key]);
				$object->array_options['options_'.$key.'_currency'] = price($object->array_options['options_'.$key], 0, $outputlangs, 0, 0, -1, $conf->currency);
				//Add value to store price with currency
				$array_to_fill=array_merge($array_to_fill, array($array_key.'_options_'.$key.'_currency' => $object->array_options['options_'.$key.'_currency']));
			}
			elseif($extrafields->attributes[$object->table_element]['type'][$key] == 'select')
			{
				$object->array_options['options_'.$key] = $extrafields->attributes[$object->table_element]['param'][$key]['options'][$object->array_options['options_'.$key]];
			}
			elseif($extrafields->attributes[$object->table_element]['type'][$key] == 'checkbox') {
				$valArray=explode(',', $object->array_options['options_'.$key]);
				$output=array();
				foreach($extrafields->attributes[$object->table_element]['param'][$key]['options'] as $keyopt=>$valopt) {
					if  (in_array($keyopt, $valArray)) {
						$output[]=$valopt;
					}
				}
				$object->array_options['options_'.$key] = implode(', ', $output);
			}
			elseif($extrafields->attributes[$object->table_element]['type'][$key] == 'date')
			{
				if (strlen($object->array_options['options_'.$key])>0)
				{
					$date = $object->array_options['options_'.$key];
					$object->array_options['options_'.$key] = dol_print_date($date, 'day');                                       // using company output language
					$object->array_options['options_'.$key.'_locale'] = dol_print_date($date, 'day', 'tzserver', $outputlangs);     // using output language format
					$object->array_options['options_'.$key.'_rfc'] = dol_print_date($date, 'dayrfc');                             // international format
				}
				else
				{
					$object->array_options['options_'.$key] = '';
					$object->array_options['options_'.$key.'_locale'] = '';
					$object->array_options['options_'.$key.'_rfc'] = '';
				}
				$array_to_fill=array_merge($array_to_fill, array($array_key.'_options_'.$key.'_locale' => $object->array_options['options_'.$key.'_locale']));
				$array_to_fill=array_merge($array_to_fill, array($array_key.'_options_'.$key.'_rfc' => $object->array_options['options_'.$key.'_rfc']));
			}
			elseif($extrafields->attributes[$object->table_element]['label'][$key] == 'datetime')
			{
				$datetime = $object->array_options['options_'.$key];
				$object->array_options['options_'.$key] = ($datetime!="0000-00-00 00:00:00"?dol_print_date($object->array_options['options_'.$key], 'dayhour'):'');                            // using company output language
				$object->array_options['options_'.$key.'_locale'] = ($datetime!="0000-00-00 00:00:00"?dol_print_date($object->array_options['options_'.$key], 'dayhour', 'tzserver', $outputlangs):'');    // using output language format
				$object->array_options['options_'.$key.'_rfc'] = ($datetime!="0000-00-00 00:00:00"?dol_print_date($object->array_options['options_'.$key], 'dayhourrfc'):'');                             // international format
				$array_to_fill=array_merge($array_to_fill, array($array_key.'_options_'.$key.'_locale' => $object->array_options['options_'.$key.'_locale']));
				$array_to_fill=array_merge($array_to_fill, array($array_key.'_options_'.$key.'_rfc' => $object->array_options['options_'.$key.'_rfc']));
			}
			elseif($extrafields->attributes[$object->table_element]['type'][$key] == 'link')
			{
				$id = $object->array_options['options_'.$key];
				if ($id != "")
				{
					$param = $extrafields->attribute_param[$key];
					$param_list=array_keys($param['options']);              // $param_list='ObjectName:classPath'
					$InfoFieldList = explode(":", $param_list[0]);
					$classname=$InfoFieldList[0];
					$classpath=$InfoFieldList[1];
					if (! empty($classpath))
					{
						dol_include_once($InfoFieldList[1]);
						if ($classname && class_exists($classname))
						{
							$tmpobject = new $classname($this->db);
							$tmpobject->fetch($id);
							// completely replace the id with the linked object name
							$object->array_options['options_'.$key] = $tmpobject->name;
						}
					}
				}
			}

			$array_to_fill=array_merge($array_to_fill, array($array_key.'_options_'.$key => $object->array_options['options_'.$key]));
		}

		return $array_to_fill;
	}


	/**
	 * Rect pdf
	 *
	 * @param	TCPDF	$pdf			Object PDF
	 * @param	float	$x				Abscissa of first point
	 * @param	float	$y		        Ordinate of first point
	 * @param	float	$l				??
	 * @param	float	$h				??
	 * @param	int		$hidetop		1=Hide top bar of array and title, 0=Hide nothing, -1=Hide only title
	 * @param	int		$hidebottom		Hide bottom
	 * @return	void
	 */
    public function printRect($pdf, $x, $y, $l, $h, $hidetop = 0, $hidebottom = 0)
    {
        if (empty($hidetop) || $hidetop==-1) $pdf->line($x, $y, $x+$l, $y);
        $pdf->line($x+$l, $y, $x+$l, $y+$h);
        if (empty($hidebottom)) $pdf->line($x+$l, $y+$h, $x, $y+$h);
        $pdf->line($x, $y+$h, $x, $y);
    }


    /**
     *  uasort callback function to Sort columns fields
     *
     *  @param	array			$a    			PDF lines array fields configs
     *  @param	array			$b    			PDF lines array fields configs
     *  @return	int								Return compare result
     */
    public function columnSort($a, $b)
    {
        if(empty($a['rank'])){ $a['rank'] = 0; }
        if(empty($b['rank'])){ $b['rank'] = 0; }
        if ($a['rank'] == $b['rank']) {
            return 0;
        }
        return ($a['rank'] > $b['rank']) ? -1 : 1;
    }

    /**
     *   	Prepare Array Column Field
     *
     *   	@param	object			$object				common object
     *   	@param	Translate		$outputlangs		langs
     *      @param	int				$hidedetails		Do not show line details
     *      @param	int				$hidedesc			Do not show desc
     *      @param	int				$hideref			Do not show ref
     *      @return	null
     */
    public function prepareArrayColumnField($object, $outputlangs, $hidedetails = 0, $hidedesc = 0, $hideref = 0)
    {
        global $conf;

        $this->defineColumnField($object, $outputlangs, $hidedetails, $hidedesc, $hideref);


        // Sorting
        uasort($this->cols, array($this, 'columnSort'));

        // Positionning
        $curX = $this->page_largeur-$this->marge_droite; // start from right

        // Array width
        $arrayWidth = $this->page_largeur-$this->marge_droite-$this->marge_gauche;

        // Count flexible column
        $totalDefinedColWidth = 0;
        $countFlexCol = 0;
        foreach ($this->cols as $colKey =>& $colDef)
        {
            if(!$this->getColumnStatus($colKey)) continue; // continue if disabled

            if(!empty($colDef['scale'])){
                // In case of column width is defined by percentage
                $colDef['width'] = abs($arrayWidth * $colDef['scale'] / 100);
            }

            if(empty($colDef['width'])){
                $countFlexCol++;
            }
            else{
                $totalDefinedColWidth += $colDef['width'];
            }
        }

        foreach ($this->cols as $colKey =>& $colDef)
        {
            // setting empty conf with default
            if(!empty($colDef['title'])){
                $colDef['title'] = array_replace($this->defaultTitlesFieldsStyle, $colDef['title']);
            }
            else{
                $colDef['title'] = $this->defaultTitlesFieldsStyle;
            }

            // setting empty conf with default
            if(!empty($colDef['content'])){
                $colDef['content'] = array_replace($this->defaultContentsFieldsStyle, $colDef['content']);
            }
            else{
                $colDef['content'] = $this->defaultContentsFieldsStyle;
            }

            if($this->getColumnStatus($colKey))
            {
                // In case of flexible column
                if(empty($colDef['width'])){
                    $colDef['width'] = abs(($arrayWidth - $totalDefinedColWidth)) / $countFlexCol;
                }

                // Set positions
                $lastX = $curX;
                $curX = $lastX - $colDef['width'];
                $colDef['xStartPos'] = $curX;
                $colDef['xEndPos']   = $lastX;
            }
        }
    }

    /**
     *  get column content width from column key
     *
     *  @param	string      $colKey     the column key
     *  @return	float                   width in mm
     */
    public function getColumnContentWidth($colKey)
    {
        $colDef = $this->cols[$colKey];
        return  $colDef['width'] - $colDef['content']['padding'][3] - $colDef['content']['padding'][1];
    }


    /**
     *  get column content X (abscissa) left position from column key
     *
     *  @param	string    $colKey    		the column key
     *  @return	float      X position in mm
     */
    public function getColumnContentXStart($colKey)
    {
        $colDef = $this->cols[$colKey];
        return  $colDef['xStartPos'] + $colDef['content']['padding'][3];
    }

    /**
     *   	get column position rank from column key
     *
     *   	@param	string		$colKey    		the column key
     *      @return	int         rank on success and -1 on error
     */
    public function getColumnRank($colKey)
    {
        if(!isset($this->cols[$colKey]['rank'])) return -1;
        return  $this->cols[$colKey]['rank'];
    }

    /**
     *  get column position rank from column key
     *
     *  @param	string		$newColKey    	the new column key
     *  @param	array		$defArray    	a single column definition array
     *  @param	string		$targetCol    	target column used to place the new column beside
     *  @param	bool		$insertAfterTarget    	insert before or after target column ?
     *  @return	int         new rank on success and -1 on error
     */
    public function insertNewColumnDef($newColKey, $defArray, $targetCol = false, $insertAfterTarget = false)
    {
        // prepare wanted rank
        $rank = -1;

        // try to get rank from target column
        if(!empty($targetCol)){
            $rank = $this->getColumnRank($targetCol);
            if($rank>=0 && $insertAfterTarget){ $rank++; }
        }

        // get rank from new column definition
        if($rank<0 && !empty($defArray['rank'])){
            $rank = $defArray['rank'];
        }

        // error: no rank
        if($rank<0){ return -1; }

        foreach ($this->cols as $colKey =>& $colDef)
        {
            if( $rank <= $colDef['rank'])
            {
                $colDef['rank'] = $colDef['rank'] + 1;
            }
        }

        $defArray['rank'] = $rank;
        $this->cols[$newColKey] = $defArray; // array_replace is used to preserve keys

        return $rank;
    }


    /**
     *  print standard column content
     *
     *  @param	PDF		    $pdf    	pdf object
     *  @param	float		$curY    	curent Y position
     *  @param	string		$colKey    	the column key
     *  @param	string		$columnText   column text
     *  @return	int         new rank on success and -1 on error
     */
    public function printStdColumnContent($pdf, &$curY, $colKey, $columnText = '')
    {
        global $hookmanager;

        $parameters=array(
            'curY' => &$curY,
            'columnText' => $columnText,
            'colKey' => $colKey
        );
        $reshook=$hookmanager->executeHooks('printStdColumnContent', $parameters, $this);    // Note that $action and $object may have been modified by hook
        if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
        if (!$reshook)
        {
            if(empty($columnText)) return;
            $pdf->SetXY($this->getColumnContentXStart($colKey), $curY); // Set curent position
            $colDef = $this->cols[$colKey];
            $pdf->writeHTMLCell($this->getColumnContentWidth($colKey), 2, $this->getColumnContentXStart($colKey), $curY, $columnText, 0, 0, 0, true, $colDef['content']['align']);
        }
    }


    /**
     *  get column status from column key
     *
     *  @param	string			$colKey    		the column key
     *  @return	float      width in mm
     */
    public function getColumnStatus($colKey)
    {
        if( !empty($this->cols[$colKey]['status'])){
            return true;
        }
        else  return  false;
    }

    /**
     * Print standard column content
     *
     * @param PDF	    $pdf            Pdf object
     * @param float     $tab_top        Tab top position
     * @param float     $tab_height     Default tab height
     * @param Translate $outputlangs    Output language
     * @param int       $hidetop        Hide top
     * @return float                    Height of col tab titles
     */
    public function pdfTabTitles(&$pdf, $tab_top, $tab_height, $outputlangs, $hidetop = 0)
    {
        global $hookmanager;

        foreach ($this->cols as $colKey => $colDef) {
            $parameters = array(
                'colKey' => $colKey,
                'pdf' => $pdf,
                'outputlangs' => $outputlangs,
                'tab_top' => $tab_top,
                'tab_height' => $tab_height,
                'hidetop' => $hidetop
            );

            $reshook = $hookmanager->executeHooks('pdfTabTitles', $parameters, $this);    // Note that $object may have been modified by hook
            if ($reshook < 0) {
                setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
            } elseif (empty($reshook)) {
                if (!$this->getColumnStatus($colKey)) continue;

                // get title label
                $colDef['title']['label'] = !empty($colDef['title']['label']) ? $colDef['title']['label'] : $outputlangs->transnoentities($colDef['title']['textkey']);

                // Add column separator
                if (!empty($colDef['border-left'])) {
                    $pdf->line($colDef['xStartPos'], $tab_top, $colDef['xStartPos'], $tab_top + $tab_height);
                }

                if (empty($hidetop)) {
                    $pdf->SetXY($colDef['xStartPos'] + $colDef['title']['padding'][3], $tab_top + $colDef['title']['padding'][0]);
                    $textWidth = $colDef['width'] - $colDef['title']['padding'][3] - $colDef['title']['padding'][1];
                    $pdf->MultiCell($textWidth, 2, $colDef['title']['label'], '', $colDef['title']['align']);
                    $this->tabTitleHeight = max($pdf->GetY() - $tab_top + $colDef['title']['padding'][2], $this->tabTitleHeight);
                }
            }
        }
        return $this->tabTitleHeight;
    }
}
