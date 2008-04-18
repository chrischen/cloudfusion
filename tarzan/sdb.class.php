<?php
/**
 * AMAZON SIMPLEDB (SDB)
 * http://sdb.amazonaws.com
 *
 * @category Tarzan
 * @package SDB
 * @version 2008.04.18
 * @copyright 2006-2008 LifeNexus Digital, Inc. and contributors.
 * @license http://opensource.org/licenses/bsd-license.php Simplified BSD License
 * @link http://tarzan-aws.googlecode.com Tarzan
 * @link http://sdb.amazonaws.com Amazon SDB
 * @see README
 */


/*%******************************************************************************************%*/
// CONSTANTS

/**
 * Specify the default queue URL.
 */
define('SDB_DEFAULT_URL', 'http://sdb.amazonaws.com/');


/*%******************************************************************************************%*/
// MAIN CLASS

/**
 * Container for all Amazon SDB-related methods.
 */
class AmazonSDB extends TarzanCore
{
	/*%******************************************************************************************%*/
	// CONSTRUCTOR

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->api_version = '2007-11-07';
		parent::__construct();
	}


	/*%******************************************************************************************%*/
	// DOMAIN

	/**
	 * Create Domain
	 * 
	 * Creates a new domain. The domain name must be unique among the domains associated with the 
	 * Access Key ID provided in the request. The CreateDomain operation might take 10 or more 
	 * seconds to complete. 
	 * 
	 * @access public
	 * @param string $domain_name (Required) The name of the domain to create.
	 * @return object A TarzanHTTPResponse response object.
	 * @see http://docs.amazonwebservices.com/AmazonSimpleDB/2007-11-07/DeveloperGuide/SDB_API_CreateDomain.html
	 */
	public function create_domain($domain_name)
	{
		$opt = array();
		$opt['DomainName'] = $domain_name;

		return $this->authenticate('CreateDomain', $opt, SDB_DEFAULT_URL);
	}

	/**
	 * List Domains
	 * 
	 * Lists all domains associated with the Access Key ID. It returns domain names up to the limit set 
	 * by MaxNumberOfDomains. A NextToken is returned if there are more than MaxNumberOfDomains domains. 
	 * Calling ListDomains successive times with the NextToken returns up to MaxNumberOfDomains more 
	 * domain names each time.
	 * 
	 * @access public
	 * @param array $opt Associative array of parameters which can have the following keys:
	 * <ul>
	 *   <li>integer MaxNumberOfDomains - (Optional) The maximum number of domain names you want returned. The range is 1 to 100.</li>
	 *   <li>string NextToken - (Optional) String that tells Amazon SimpleDB where to start the next list of domain names.</li>
	 * </ul>
	 * @return object A TarzanHTTPResponse response object.
	 * @see http://docs.amazonwebservices.com/AmazonSimpleDB/2007-11-07/DeveloperGuide/SDB_API_ListDomains.html
	 */
	public function list_domains($opt = null)
	{
		return $this->authenticate('ListDomains', $opt, SDB_DEFAULT_URL);
	}

	/**
	 * Delete Domain
	 * 
	 * Deletes a domain. Any items (and their attributes) in the domain are deleted as well. The DeleteDomain 
	 * operation might take 10 or more seconds to complete. Running DeleteDomain on a domain that does not 
	 * exist or running the function multiple times using the same domain name will not result in an error 
	 * response.
	 * 
	 * @access public
	 * @param string $domain_name (Required) The name of the domain to delete.
	 * @return object A TarzanHTTPResponse response object.
	 * @see http://docs.amazonwebservices.com/AmazonSimpleDB/2007-11-07/DeveloperGuide/SDB_API_DeleteDomain.html
	 */
	public function delete_domain($domain_name)
	{
		$opt = array();
		$opt['DomainName'] = $domain_name;

		return $this->authenticate('DeleteDomain', $opt, SDB_DEFAULT_URL);
	}


	/*%******************************************************************************************%*/
	// ATTRIBUTES

	/**
	 * Put Attributes
	 * 
	 * Creates or replaces attributes in an item. You specify new attributes using a combination of 
	 * the Attribute.X.Name and Attribute.X.Value parameters.
	 * 
	 * @access public
	 * @param string $domain_name (Required) The name of the domain to create.
	 * @param string $item_name (Required) The name of the item/object to create. This will contain various key-value pairs.
	 * @param array $keypairs (Required) Associative array of parameters which are treated as key-value pairs.
	 * @param boolean $replace (Optional) Whether to replace an existing $item_name with this one. Defaults to false.
	 * @return object A TarzanHTTPResponse response object.
	 * @see http://docs.amazonwebservices.com/AmazonSimpleDB/2007-11-07/DeveloperGuide/SDB_API_PutAttributes.html
	 */
	public function put_attributes($domain_name, $item_name, $keypairs, $replace = null)
	{
		$opt = array();
		$opt['DomainName'] = $domain_name;
		$opt['ItemName'] = $item_name;

		$count = 0;
		foreach ($keypairs as $k => $v)
		{
			$opt['Attribute.' . (string) $count . '.Name'] = $k;
			$opt['Attribute.' . (string) $count . '.Value'] = $v;
			$count++;
		}

		if ($replace)
		{
			$opt['Attribute.Replace'] = 'true';
		}

		return $this->authenticate('PutAttributes', $opt, SDB_DEFAULT_URL);
	}

	/**
	 * Get Attributes
	 * 
	 * Returns all of the attributes associated with the item. Optionally, the attributes returned 
	 * can be limited to one or more specified attribute name parameters. If the item does not exist 
	 * on the replica that was accessed for this operation, an empty set is returned. The system 
	 * does not return an error as it cannot guarantee the item does not exist on other replicas.
	 * 
	 * @access public
	 * @param string $domain_name (Required) The name of the domain to create.
	 * @param string $item_name (Required) The name of the item/object to create. This will contain various key-value pairs.
	 * @param mixed $keys (Optional) The name of the key (attribute) in the key-value pair. Supports a string value (for single keys) or an indexed array (for multiple keys).
	 * @return object A TarzanHTTPResponse response object.
	 * @see http://docs.amazonwebservices.com/AmazonSimpleDB/2007-11-07/DeveloperGuide/SDB_API_GetAttributes.html
	 */
	public function get_attributes($domain_name, $item_name, $keys = null)
	{
		$opt = array();
		$opt['DomainName'] = $domain_name;
		$opt['ItemName'] = $item_name;

		if ($keys)
		{
			if (is_array($keys))
			{
				$count = 0;
				foreach ($keys as $key)
				{
					$opt['AttributeName.' . (string) $count] = $key;
					$count++;
				}
			}
			else
			{
				$opt['AttributeName'] = $keys;
			}
		}

		return $this->authenticate('GetAttributes', $opt, SDB_DEFAULT_URL);
	}

	/**
	 * Delete Attributes
	 * 
	 * Deletes one or more attributes associated with the item. If all attributes of an item are 
	 * deleted, the item is deleted. If you specify DeleteAttributes without attributes or values, 
	 * all the attributes for the item are deleted. DeleteAttributes is an idempotent operation; 
	 * running it multiple times on the same item or attribute does not result in an error response.
	 * 
	 * @access public
	 * @param string $domain_name (Required) The name of the domain to create.
	 * @param string $item_name (Required) The name of the item/object to create. This will contain various key-value pairs.
	 * @param mixed $keys (Optional) The name of the key(s) (attribute(s)) to delete from the item. Supports a string value (for single keys), an indexed array (for multiple keys), or an associative array containing one or more key-value pairs (for deleting specific values).
	 * @return object A TarzanHTTPResponse response object.
	 * @see http://docs.amazonwebservices.com/AmazonSimpleDB/2007-11-07/DeveloperGuide/SDB_API_DeleteAttributes.html
	 */
	public function delete_attributes($domain_name, $item_name, $keys = null)
	{
		$opt = array();
		$opt['DomainName'] = $domain_name;
		$opt['ItemName'] = $item_name;

		// Do we have a key?
		if ($keys)
		{
			// An array?
			if (is_array($keys))
			{
				// Indexed array of Attribute Names?
				if ($this->util->ready($keys[0]))
				{
					for ($x = 0, $i = count($keys); $x < $i; $x++)
					{
						$opt['Attribute.' . (string) $i . '.Name'] = $keys[$i];
					}
				}

				// Associative array of Name/Value pairs.
				else
				{
					$count = 0;
					foreach ($keys as $k => $v)
					{
						$opt['Attribute.' . (string) $count . '.Name'] = $k;
						$opt['Attribute.' . (string) $count . '.Value'] = $v;
						$count++;
					}
				}
			}

			// Single string Attribute Name.
			else
			{
				$opt['Attribute.Name'] = $keys;
			}
		}

		return $this->authenticate('DeleteAttributes', $opt, SDB_DEFAULT_URL);
	}


	/*%******************************************************************************************%*/
	// QUERY

	/**
	 * Query
	 * 
	 * Returns a set of ItemNames that match the query expression. Query operations that run longer 
	 * than 5 seconds will likely time-out and return a time-out error response. A Query with no 
	 * QueryExpression matches all items in the domain.
	 * 
	 * @access public
	 * @param string $domain_name (Required) The name of the domain to create.
	 * @param array $opt Associative array of parameters which can have the following keys:
	 * <ul>
	 *   <li>integer MaxNumberOfItems - (Optional) The maximum number of item names you want returned. The range is 1 to 250, defaults to 100.</li>
	 *   <li>string NextToken - (Optional) String that tells Amazon SimpleDB where to start the next list of domain names.</li>
	 * </ul>
	 * @param string $expression (Optional) The SimpleDB query expression to use.
	 * @return object A TarzanHTTPResponse response object.
	 * @see http://docs.amazonwebservices.com/AmazonSimpleDB/2007-11-07/DeveloperGuide/SDB_API_Query.html
	 */
	public function query($domain_name, $opt = null, $expression = null)
	{
		$opt = array();
		$opt['DomainName'] = $domain_name;
		$opt['QueryExpression'] = $expression;

		return $this->authenticate('Query', $opt, SDB_DEFAULT_URL);
	}
}
?>