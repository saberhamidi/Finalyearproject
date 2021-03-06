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
					$participant = $db->fetchAll("SELECT email, firstname, lastname, loyalty_balance, twitter_id from customers where email = ?",[$pid])[0][0];
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

			//validate the form before saving
			$validate = $this->request->data['Campaign'];
			unset($validate['page']);
			if(array_key_exists('hashtag', $validate) && $validate['type'] !='Re-tweeting Promotion'){
				unset($validate['hashtag']);
			}
			foreach ($validate as $key => $value) {
				if(trim($value) ==''){
						$this->Flash->error(__('One or more required feilds are not filled in!'));
						$this->redirect(array('action'=>'add'));
				}
			}

			if ($this->Campaign->save($this->request->data)) {
				/*
				Construct a file name combining the word 'campaign' with the id of
				the current campaign. */
				$fname = 'campaign'.$this->Campaign->id.'.ctp';

				//Create a file and assign the above file name to it, then place it in the Pages folder
				$file = new File('app/View/Pages/'.$fname,true);

				//write contents of the campaign to the file.
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

		$thisCmpgn = $this->Campaign->find('first', array('conditions' => array('Campaign.' . $this->Campaign->primaryKey => $id)));
		if ($this->request->is(array('post', 'put'))) {

			//prevent parameter manipulation, and restrics users from changing the campaign type when it is already deoployed
			if(isset($this->request->data['Campaign']['type'])){
				if($thisCmpgn['Campaign']['deployed'] && $thisCmpgn['Campaign']['type'] !=$this->request->data['Campaign']['type']){
						$this->Flash->error(__('Your not allowed to change the campaign type as it is already deployed!'));
						$this->redirect(array('action'=>'edit/'.$id));
				}
			}

			//prevent parameter manipulation, and restrics users from changing the campaign type when it is already deoployed
			if(isset($this->request->data['Campaign']['hashtag'])){
				if($thisCmpgn['Campaign']['deployed'] && $thisCmpgn['Campaign']['hashtag'] !=$this->request->data['Campaign']['hashtag']){
						$this->Flash->error(__('Your not allowed to change the campaign hashtag as it is already deployed!'));
						$this->redirect(array('action'=>'edit/'.$id));
				}
			}

			if(isset($this->request->data['Campaign']['type'])){
				if($this->request->data['Campaign']['type'] != 'Re-tweeting Promotion'){
					$this->request->data['Campaign']['hashtag'] = '';
				}
			}

			$this->Campaign->id = $id;

			//validate the form before saving
			$validate = $this->request->data['Campaign'];
			unset($validate['page']);
			if(array_key_exists('hashtag', $validate) && $validate['type'] !='Re-tweeting Promotion'){
				unset($validate['hashtag']);
			}
			foreach ($validate as $key => $value) {
				if(trim($value) ==''){
						$this->Flash->error(__('One or more required feilds are not filled in!'));
						$this->redirect(array('action'=>'edit/'.$id));
				}
			}
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
			$this->request->data = $thisCmpgn;
			$file = new File('app/View/Pages/'.$fname,true);

			$this->set('content', $file->read());
			$this->set('campaignStatues', $thisCmpgn['Campaign']['deployed']);
			$this->set('campaignType', $thisCmpgn['Campaign']['type']);

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
		$cmpgn = $this->Campaign->find('first', array('conditions' => array('Campaign.id' => $id)));
			if ($this->request->is('post')) {
				if ($cmpgn['Campaign']['deployed']) {
					$this->Flash->error(__('This campaign is already Deployed!'));
					$this->redirect(array('action'=>'view/'.$id));
				}

				else{

					$db = ConnectionManager::getDataSource('default');
					$participants = $db->fetchAll('SELECT email from customers');

					$fname = 'campaign'.$cmpgn['Campaign']['id'].'.ctp';
					$file = new File('app/View/Pages/'.$fname,true);
					echo strrchr($file->read(), '.jpg');
					die();
					foreach ($participants as $participant) {
						$db->query("INSERT INTO participants (campaign_id, participant_id) VALUES($id, '".$participant[0]['email']."')");
						$Email = new CakeEmail();
						$Email->config('gmail');
						$Email->to($participant[0]['email'])
						    ->subject($cmpgn['Campaign']['name']);

						if($cmpgn['Campaign']['type'] == 'Re-tweeting Promotion'){
							$Email->attachments(array('Promotion.jpg' => 'app/webroot/img/campaigns/c'.$id.'.jpg'));
							$Email->send(strip_tags($file->read()).' Hashtag: '.$cmpgn['Campaign']['hashtag']);
						}
						else{
					   		$Email->send('We are holding another amazing Recommend for Reward campaign! Please Share this link with your friends and families and keep increasing your reward NOW! http://localhost:8080/pages/campaign'.$id.'/'.$db->lastInsertId());
					   	}
					}
					$this->Flash->success(__('The campaign successifully deployed to all customers!'));
					$this->Campaign->id = $id;
					$this->Campaign->save(array('Campaign'=>['deployed' => true]));
					$this->redirect(array('action'=>'view/'.$id));
				}

		} 
	}
}
