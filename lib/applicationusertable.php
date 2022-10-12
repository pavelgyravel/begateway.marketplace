<?php
namespace BeGateway\Module\Marketplace;

use Bitrix\Main;
use Bitrix\Main\Type;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class ApplicationUserTable
 * 
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> APPLICATION_ID int mandatory
 * <li> USER_ID string(255) mandatory
 * </ul>
 *
 * @package Bitrix\Bemarketplace
 **/

class ApplicationUserTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_bemarketplace_application_user';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			new Main\Entity\IntegerField(
				'ID',
				[
					'primary' => true,
					'autocomplete' => true,
					'title' => Loc::getMessage('APPLICATION_USER_ENTITY_ID_FIELD')
				]
			),
			new Main\Entity\IntegerField(
				'APPLICATION_ID',
				[
					'required' => true,
					'title' => Loc::getMessage('APPLICATION_USER_ENTITY_APPLICATION_ID_FIELD')
				]
			),
			new Main\Entity\StringField(
				'USER_ID',
				[
					'required' => true,
					'validation' => [__CLASS__, 'validateUserId'],
					'title' => Loc::getMessage('APPLICATION_USER_ENTITY_USER_ID_FIELD')
				]
			),
		];
	}

	/**
	 * Returns validators for USER_ID field.
	 *
	 * @return array
	 */
	public static function validateUserId()
	{
		return [
			new Main\Entity\Validator\Length(null, 255),
		];
	}
}