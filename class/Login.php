<?php

class Login {

    private $_idlogin;
    private $_email;
    private $_motpasse;
    private $_pseudo;
    private $_tmpcx;
    private $_nomEquipe;

    public function __construct($idlogin, $email, $motpasse, $pseudo, $tmpcx, $nomEquipe) {
        $this->setIdlogin($idlogin);
        $this->setEmail($email);
        $this->setMotpasse($motpasse);
        $this->setPseudo($pseudo);
        $this->setTmpcx($tmpcx);
        $this->setNomEquipe($nomEquipe);
    }

    public function setTmpcx($tmpcx) {
        $this->_tmpcx = (int) $tmpcx;
    }

    public function getTmpcx() {
        return $this->_tmpcx;
    }

    public function setIdlogin($id) {
        $this->_idlogin = (int) $id;
    }

    public function getIdlogin() {
        return $this->_idlogin;
    }

    public function setEmail($mail) {
        $this->_email = $mail;
    }

    public function getEmail() {
        return $this->_email;
    }

    public function setMotpasse($passe) {
        $this->_motpasse = $passe;
    }

    public function getMotpasse() {
        return $this->_motpasse;
    }

    public function setPseudo($pseudo) {
        $this->_pseudo = $pseudo;
    }

    public function getPseudo() {
        return $this->_pseudo;
    }

    public function setNomEquipe($nom) {
        $this->_nomEquipe = $nom;
    }

    public function getNomEquipe() {
        return $this->_nomEquipe;
    }

}
