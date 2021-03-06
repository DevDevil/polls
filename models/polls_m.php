<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * The galleries module enables users to create albums, upload photos and manage their existing albums.
 *
 * @author 	Victor Michnowicz
 * @category 	Modules
 *
 */
class Polls_m extends MY_Model {
	
	/**
	 * Get all polls
	 *
	 * @author Victor Michnowicz
	 * @access public
	 * @return mixed
	 */
	public function get_all()
	{
		$results = array();

		$query = $this->db->get('polls');
		
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
   			{
   				// Is this poll open
   				$close_date = ( $row->close_date ) ? $row->close_date : time() * 2;
				$open_date = $row->open_date;
				
				// If poll is open
				if ( $close_date > time() AND $open_date < time() )
				{
					$poll_open = TRUE;
				}
				// If poll is closed
				else
				{
					$poll_open = FALSE;
				}
			
				$results[] = array(
					'id' 				=> $row->id,
					'slug' 				=> $row->slug,
					'title' 			=> $row->title,
					'description' 		=> $row->description,
					'open_date' 		=> $open_date,
					'close_date' 		=> $close_date,
					'is_open' 			=> $poll_open,
					'created' 			=> $row->created,
					'last_updated' 		=> $row->last_updated,
					'type' 				=> $row->type,
					'comments_enabled' 	=> $row->comments_enabled,
					'members_only' 		=> $row->members_only
				);
			}
			
			// Return all polls
			return $results;
		}
		
		// If the query returned no results, return FALSE
		return FALSE;
	}
	
	/**
	 * Make sure a poll exists
	 *
	 * @author Victor Michnowicz
	 * @access public
	 * @param int poll ID
	 * @return bool
	 */	
	public function poll_exists($id)
	{	
		$this->db
			->select('id')
			->from('polls')
			->where('id', $id)
			->limit(1);
		
		$query = $this->db->get();
		
		// If this poll exists
		if ($query->num_rows() > 0)
		{			
			return TRUE;
		}
		
		// If the poll does not exist
		return FALSE;
	}
	
	/**
	 * Get the poll ID from its slug
	 *
	 * @author Victor Michnowicz
	 * @access public
	 * @param int poll slug
	 * @return mixed
	 */		
	public function get_poll_id_from_slug($slug)
	{
		$this->db
			->select('id')
			->from('polls')
			->where('slug', $slug)
			->limit(1);
			
		$query = $this->db->get();

		if ($query->num_rows() > 0)
		{
			$row = $query->row(); 
			return $row->id;
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Return poll data
	 *
	 * @author Victor Michnowicz
	 * @access public
	 * @param int poll ID
	 * @return mixed
	 */
	public function get_poll_by_id($id)
	{	
		$this->db
			->select('*')
			->from('polls')
			->where('id', $id)
			->limit(1);
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0)
		{
			$row = $query->row(); 
			
			// Is this poll open
			$close_date = ( $row->close_date ) ? $row->close_date : time() * 2;
			$open_date = $row->open_date;
			
			// If poll is open
			if ( $close_date > time() AND $open_date < time() )
			{
				$poll_open = TRUE;
			}
			// If poll is closed
			else
			{
				$poll_open = FALSE;
			}
			
			$data = array(
				'id' 				=> $row->id,
				'slug' 				=> $row->slug,
				'title' 			=> $row->title,
				'description' 		=> $row->description,
				'open_date' 		=> $open_date,
				'close_date' 		=> $close_date,
				'is_open' 			=> $poll_open,
				'type' 				=> $row->type,
				'created' 			=> $row->created,
				'comments_enabled' 	=> (bool)$row->comments_enabled,
				'members_only' 		=> (bool)$row->members_only
			);
			
			return $data;
		}
		
		// If the poll does not exist
		return FALSE;
	}

	/**
	 * Insert a new poll into the database
	 *
	 * @author Victor Michnowicz
	 * @access public
	 * @param array $input The data to insert (a copy of $_POST)
	 * @return bool
	 */
	public function add($input)
	{
		// Prep data for insertion into the database
		$data = array(
			'title' 			=> $input['title'],
			'slug' 				=> $input['slug'],
			'description' 		=> $input['description'],
			'open_date' 		=> (int)date_to_timestamp($input['open_date']),
			'close_date' 		=> (int)date_to_timestamp($input['close_date']),
			'type' 				=> $input['type'],
			'comments_enabled' 	=> (int)$input['comments_enabled'],
			'members_only' 		=> (int)$input['members_only'],
			'created' 			=> time()
		);
		
		// Insert that data
		$this->db->insert('polls', $data);
		
		return TRUE;

	}
	
	/**
	 * Delete a new poll from the database
	 *
	 * @author Victor Michnowicz
	 * @access public
	 * @param int poll ID
	 * @return bool
	 */	
	public function delete($id)
	{		
		$this->db
			->from('polls')
			->where('id', $id)
			->delete();
			
		return TRUE;
	}

	/**
	 * Update an existing poll
	 *
	 * @author Victor Michnowicz
	 * @access public
	 * @param int $id The ID of the poll to update
	 * @param array $input The POST data to use for updating the DB record
	 * @return bool
	 */
	public function update($id, $input)
	{
		// Get the poll data
		$data = array(
			'title' 			=> $input['title'],
			'slug' 				=> $input['slug'],
			'description' 		=> $input['description'],
			'open_date' 		=> (int)date_to_timestamp($input['open_date']),
			'close_date' 		=> (int)date_to_timestamp($input['close_date']),
			'type' 				=> $input['type'],
			'comments_enabled' 	=> (int)$input['comments_enabled'],
			'members_only' 		=> (int)$input['members_only'],
			'last_updated' 		=> time()
		);
		
		// Update poll data
		$this->db
			->where('id', $id)
			->update('polls', $data);
		
		return TRUE;
	}

}