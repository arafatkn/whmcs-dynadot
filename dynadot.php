<?php
/**
 * @title   WHMCS Dynadot Domain Register Module
 *
 * @author  Arafat Islam
 * @license MIT
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require_once __DIR__ . "/includes/Dynadot.php";
require_once __DIR__ . "/includes/Http/Request.php";

use Arafatkn\WhmcsDynadot\Dynadot;

/**
 * Define module related metadata.
 *
 * @return array
 */
function dynadot_MetaData(): array
{
    return [
        'DisplayName' => 'Dynadot Registrar Module for WHMCS',
        'APIVersion' => '1.1',
    ];
}

/**
 * Define registrar configuration options.
 *
 * @return array
 */
function dynadot_getConfigArray(): array
{
    return [
        "api_key" => [
            "Type" => "text",
            "Size" => "20",
            "Description" => "Dynadot API v3 Key here."
        ]
    ];
}

/**
 * Register a domain.
 *
 * Attempt to register a domain with the domain registrar.
 *
 * This is triggered when the following events occur:
 * * Payment received for a domain registration order
 * * When a pending domain registration order is accepted
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @return array
 */
function dynadot_RegisterDomain(array $params): array
{
    return (new Dynadot($params))->register();
}

/**
 * Transfer a domain.
 *
 * Attempt to create a domain transfer request for a given domain.
 *
 * This is triggered when the following events occur:
 * * Payment received for a domain transfer order
 * * When a pending domain transfer order is accepted
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @return array
 */
function dynadot_TransferDomain(array $params): array
{
    return (new Dynadot($params))->transfer();
}

/**
 * Renew a domain.
 *
 * Attempt to renew/extend a domain for a given number of years.
 *
 * This is triggered when the following events occur:
 * * Payment received for a domain renewal order
 * * When a pending domain renewal order is accepted
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/domain-registrars/module-parameters/
 *
 * @return array
 */
function dynadot_RenewDomain(array $params): array
{
    return (new Dynadot($params))->renew();
}

/**
 * Fetch current nameservers.
 *
 * This function should return an array of nameservers for a given domain.
 *
 * @param array $params common module parameters
 *
 * @return array
 */
function dynadot_GetNameservers($params): array
{
    return (new Dynadot($params))->getNameServers();
}

/**
 * Save nameserver changes.
 *
 * This function should submit a change of nameservers request to the
 * domain registrar.
 *
 * @param array $params common module parameters
 *
 * @return array
 */
function dynadot_SaveNameservers($params): array
{
    return (new Dynadot($params))->saveNameServers();
}

/**
 * Get registrar lock status.
 *
 * Also known as Domain Lock or Transfer Lock status.
 *
 * @param array $params common module parameters
 *
 * @return string|array Lock status or error message
 */
function dynadot_GetRegistrarLock($params)
{
    return (new Dynadot($params))->getRegistrarLock();
}

/**
 * Set registrar lock status.
 *
 * @param array $params common module parameters
 *
 * @return array
 */
function dynadot_SaveRegistrarLock($params): array
{
    return (new Dynadot($params))->saveRegistrarLock();
}

/**
 * Enable/Disable ID Protection.
 *
 * @param array $params common module parameters
 *
 * @return array
 */
function dynadot_IDProtectToggle($params): array
{
    return (new Dynadot($params))->togglePrivacyProtection();
}

/**
 * Request EEP Code.
 *
 * Supports both displaying the EPP Code directly to a user or indicating
 * that the EPP Code will be emailed to the registrant.
 *
 * @param array $params common module parameters
 *
 * @return array
 *
 */
function dynadot_GetEPPCode($params): array
{
    return (new Dynadot($params))->getEppCode();
}