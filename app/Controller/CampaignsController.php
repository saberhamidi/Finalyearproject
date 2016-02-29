<?php
App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('CakeEmail', 'Network/Email');
/**
 * Campaigns Controller
 *
 * @property Campaign $Campaign
 * @property PaginatorComponent $Paginator
 */
class CampaignsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Campaign->recursive = 0;
		$this->set('campaigns', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Campaign->exists($id)) {
			throw new NotFoundException(__('Invalid campaign'));
		}
		$options = array('conditions' => array('Campaign.' . $this->Campaign->primaryKey => $id));
		$this->set('campaign', $this->Campaign->find('first', $options));

			//fetch participant ids
			$pids = $this->Campaign->Participant->find('list', array('fields' => 'Participant.participant_id', 'conditions'=>'Participant.campaign_id =='.$id));

			//stor particpant ids
			$participants=array();

			//connect to the database and fetch full details of each participant
			$db = ConnectionManager::getDataSource('default');
			if(!empty($pids)){
				foreach ($pids as $key => $pid) {
					$participant = $db->fetchAll("SELECT email, firstname, lastname, loyalty_balance from customers where email = ?",[$pid])[0][0];
					array_push($participants, $participant);
				}
			}

			$this->set('participants',$participants);

	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Campaign->create();
			if ($this->Campaign->save($this->request->data)) {

				$fname = 'campaign'.$this->Campaign->id.'.ctp';
				$file = new File('app/View/Pages/'.$fname,true);
				$file->write($this->request->data['Campaign']['page']);

				$this->Flash->success(__('The campaign has been successifully created!.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The campaign could not be created. Please, try again.'));
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {

		//name of the file to be searched
		$fname = 'campaign'.$id.'.ctp';

		if (!$this->Campaign->exists($id)) {
			throw new NotFoundException(__('Invalid campaign'));
		}
		if ($this->request->is(array('post', 'put'))) {
			$this->Campaign->id = $id;
			if ($this->Campaign->save($this->request->data)) {

				$file = new File('app/View/Pages/'.$fname,true);
				$file->write($this->request->data['Campaign']['page']);

				$this->Flash->success(__('The campaign has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The campaign could not be saved. Please, try again.'));
			}
		} 
		else {
			$options = array('conditions' => array('Campaign.' . $this->Campaign->primaryKey => $id));
			$this->request->data = $this->Campaign->find('first', $options);

			$file = new File('app/View/Pages/'.$fname,true);

			$this->set('content', $file->read());

		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Campaign->id = $id;
		if (!$this->Campaign->exists()) {
			throw new NotFoundException(__('Invalid campaign'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Campaign->delete()) {
			$fname = 'campaign'.$id.'.ctp';
			$file = new File('app/View/Pages/'.$fname,true);
			$file->delete();

			//update participant table
			$db = ConnectionManager::getDataSource('default');
			$db->query("DELETE FROM participants WHERE campaign_id = ?",[$id]);

			$this->Flash->success(__('The campaign has been deleted.'));
		} else {
			$this->Flash->error(__('The campaign could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	public $helpers = array('Wysiwyg.Wysiwyg' => array('editor' => 'Ck'));

	public function deploy($id = nulll){

			if ($this->request->is('post')) {
				$deployed = $this->Campaign->find('first', array('conditions' => array('Campaign.id' => $id)))['Campaign']['deployed'];
				if ($deployed) {
					$this->Flash->error(__('This campaign is already Deployed!'));
					$this->redirect(array('action'=>'view/'.$id));
				}

				else{

					$db = ConnectionManager::getDataSource('default');
					$participants = $db->fetchAll('SELECT email from customers');

					foreach ($participants as $participant) {
						$db->query("INSERT INTO participants (campaign_id, participant_id) VALUES($id, '".$participant[0]['email']."')");
						$Email = new CakeEmail();
						$Email->config('gmail');
						$Email->to($participant[0]['email'])
						    ->subject('Christmas Campaign');
					   	$Email->send('Share this compaing with your friends and families and increase your reward! http://localhost:8080/pages/campaign'.$id.'/'.$db->lastInsertId());
					}

					$this->Flash->success(__('The campaign successifully deployed to all customers!'));
					$this->Campaign->id = $id;
					$this->Campaign->save(array('Campaign'=>['deployed' => true]));
					$this->redirect(array('action'=>'view/'.$id));
				}

		} 
	}
}
