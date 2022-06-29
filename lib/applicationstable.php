<?php
namespace BeGateway\Module\Marketplace;

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\ORM\Data\DataManager,
	Bitrix\Main\ORM\Fields\IntegerField,
	Bitrix\Main\ORM\Fields\StringField,
	Bitrix\Main\ORM\Fields\Validators\LengthValidator;

Loc::loadMessages(__FILE__);

/**
 * Class ApplicationsTable
 * 
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> SITE_ID string(2) mandatory
 * <li> CLIENT_ID string(255) mandatory
 * <li> CLIENT_SECRET string(255) mandatory
 * <li> HOST string(255) mandatory
 * </ul>
 *
 * @package Bitrix\Bemarketplace
 **/

class ApplicationsTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_bemarketplace_applications';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			new IntegerField(
				'ID',
				[
					'primary' => true,
					'autocomplete' => true,
					'title' => Loc::getMessage('APPLICATIONS_ENTITY_ID_FIELD')
				]
			),
			new StringField(
				'SITE_ID',
				[
					'required' => true,
					'validation' => [__CLASS__, 'validateSiteId'],
					'title' => Loc::getMessage('APPLICATIONS_ENTITY_SITE_ID_FIELD')
				]
			),
			new StringField(
				'CLIENT_ID',
				[
					'required' => true,
					'validation' => [__CLASS__, 'validateClientId'],
					'title' => Loc::getMessage('APPLICATIONS_ENTITY_CLIENT_ID_FIELD')
				]
			),
			new StringField(
				'CLIENT_SECRET',
				[
					'required' => true,
					'validation' => [__CLASS__, 'validateClientSecret'],
					'title' => Loc::getMessage('APPLICATIONS_ENTITY_CLIENT_SECRET_FIELD')
				]
			),
			new StringField(
				'HOST',
				[
					'required' => true,
					'validation' => [__CLASS__, 'validateHost'],
					'title' => Loc::getMessage('APPLICATIONS_ENTITY_HOST_FIELD')
				]
			),
		];
	}

	/**
	 * Returns validators for SITE_ID field.
	 *
	 * @return array
	 */
	public static function validateSiteId()
	{
		return [
			new LengthValidator(null, 2),
		];
	}

	/**
	 * Returns validators for CLIENT_ID field.
	 *
	 * @return array
	 */
	public static function validateClientId()
	{
		return [
			new LengthValidator(null, 255),
		];
	}

	/**
	 * Returns validators for CLIENT_SECRET field.
	 *
	 * @return array
	 */
	public static function validateClientSecret()
	{
		return [
			new LengthValidator(null, 255),
		];
	}

	/**
	 * Returns validators for HOST field.
	 *
	 * @return array
	 */
	public static function validateHost()
	{
		return [
			new LengthValidator(null, 255),
		];
	}
}