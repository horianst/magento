<?php

namespace Urgent\Cargus\Api\Data;

interface AWBExpeditiiInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const ORDER_ID = 'order_id';
    const PICKUP_LOCATION_ID = 'pickup_location_id';
    const COD_BARA = 'cod_bara';
    const TIMESTAMP = 'timestamp';
    const NUME_DEST = 'nume_dest';
    const JUDET_ID = 'judet_id';
    const JUDET_DEST = 'judet_dest';
    const LOCALITATE_ID = 'localitate_id';
    const LOCALITATE_DEST = 'localitate_dest';
    const ADRESA_DEST = 'adresa_dest';
    const CONTACT_DEST = 'contact_dest';
    const TELEFON_DEST = 'telefon_dest';
    const EMAIL_DEST = 'email_dest';
    const PLICURI = 'plicuri';
    const COLETE = 'colete';
    const KILOGRAME = 'kilograme';
    const VALOARE_DECLARATA = 'valoare_declarata';
    const RAMBURS_NUMERAR = 'ramburs_numerar';
    const RAMBURS_CONT = 'ramburs_cont';
    const RAMBURS_ALT = 'ramburs_alt';
    const PLATITOR_EXPEDITIE = 'platitor_expeditie';
    const LIVRARE_SAMBATA = 'livrare_sambata';
    const LIVRARE_DIMINEATA = 'livrare_dimineata';
    const DESCHIDERE_COLET = 'deschidere_colet';
    const OBSERVATII = 'observatii';
    const CONTINUT = 'continut';
    const STATUS = 'status';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);
}
