<?php

namespace App\DAO;

use App\Util\DataBase as DB;
use \PDO;
use Carbon\Carbon;


class UsuarioDAO
{

    public function __construct()
    {
    }


    public static function createUser($p)
    {
        try {
            $db = DB::getConnection();
            $stm = $db->prepare("INSERT INTO usuarios(id_colaborador,id_perfil,usuario,contrasena) VALUES(?,?,?,?)");
            $stm->bindParam(1, $p->id_colaborador);
            $stm->bindParam(2, $p->id_perfil);
            $stm->bindParam(3, $p->username);
            $stm->bindParam(4, $p->password);
            $stm->execute();
            return $db->lastInsertId();
        } catch (\Throwable $th) {
            return ["ErrorApi" => $th->getMessage()];
        }
    }

    public static function getUsuarios()
    {
        try {
            $db = DB::getConnection();
            $stm = $db->prepare("SELECT nombre,paterno,materno,usuarios.id,usuario,perfil.perfil 
            FROM usuarios 
            INNER JOIN perfil ON perfil.id=usuarios.id_perfil
            INNER JOIN colaborador ON colaborador.id=usuarios.id_colaborador
            INNER JOIN personas ON personas.id=colaborador.id_persona");
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return ["ErrorApi" => $th->getMessage()];
        }
    }
    public static function getUsuario($id)
    {
        try {
            $db = DB::getConnection();
            $stm = $db->prepare("SELECT nombre,paterno,materno,usuarios.id,usuario,perfil.perfil 
            FROM usuarios 
            INNER JOIN perfil ON perfil.id=usuarios.id_perfil
            INNER JOIN colaborador ON colaborador.id=usuarios.id_colaborador
            INNER JOIN personas ON personas.id=colaborador.id_persona WHERE usuarios.id=?");
            $stm->bindParam(1, $id);
            $stm->execute();
            return $stm->fetch(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return ["ErrorApi" => $th->getMessage()];
        }
    }

    public static function updateUsuario($id, $p)
    {
        try {
            $db = DB::getConnection();
            $stm = $db->prepare("UPDATE usuarios SET id_perfil=?,contrasena=? WHERE id=?");
            $stm->bindParam(1, $p->perfil);
            $stm->bindParam(2, $p->password);
            $stm->bindParam(3, $id);
            $stm->execute();
            return $stm->rowCount();
        } catch (\Throwable $th) {
            return ["ErrorApi" => $th->getMessage()];
        }
    }

    public static function deleteUsuario($id)
    {
        try {
            $db = DB::getConnection();
            $stm = $db->prepare("UPDATE usuarios SET estatus=0 WHERE id=?");
            $stm->bindParam(1, $id);
            $stm->execute();
            return $stm->rowCount();
        } catch (\Throwable $th) {
            return ["ErrorApi" => $th->getMessage()];
        }
    }

    public static function addModulos($id_usuario, $modulos)
    {
        $db = DB::getConnection();
        try {
            $db->beginTransaction();

            $stm = $db->prepare("DELETE FROM usuario_modulo WHERE id_usuario=?");
            $stm->bindParam(1, $id_usuario, PDO::PARAM_INT);
            $stm->execute();

            $stm = $db->prepare("INSERT INTO usuario_modulo(id_usuario,id_modulo) VALUES(:usuario,:modulo)");

            foreach ($modulos as $id_modulo) {
                $stm->bindParam(":usuario", $id_usuario, PDO::PARAM_INT);
                $stm->bindParam(":modulo", $id_modulo, PDO::PARAM_INT);
                $stm->execute();
            }

            $db->commit();
            return 1;
        } catch (\Throwable $th) {
            $db->rollBack();
            return ["ErrorApi" => $th->getMessage()];
        }
    }
}
