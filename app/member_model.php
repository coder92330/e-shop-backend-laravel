<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use App\Custom\Hasher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\DB;

class member_model extends Authenticatable implements JWTSubject
{
    use Notifiable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'password',
        'level_id',
        'parent',
        'id_path'
    ];

    protected $table = "members";

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Custom attributes for data model.
     *
     * @var array
     */
    public $appends = ['hashid'];

    /**
     * Encodes the user id and returns the unique hash.
     *
     * @return string Hashid
     */
    public function hashid()
    {
        return Hasher::encode($this->id);
    }

    /**
     * Returns the hashid for a custom attribute.
     *
     * @return string Hashid
     */
    public function getHashidAttribute()
    {
        return $this->hashid();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Allows us to customize the password notification email.
     * See: App/Notifications/ResetPassword.php
     *
     * @param string
     */
    public function sendPasswordResetNotification($token)
    {
        $email = $this->getEmailForPasswordReset();
        $user = $this::where('email', $email)->first();
        $this->notify(new ResetPasswordNotification($token, $user->id));
    }

    public function GetMemberIdMyName($member_name)
    {
        $member = $this->where('name', $member_name)->firstOrFail();
        return $member->id;
    }
    public function GetMemberInfo($member_id)
    {
        $member = $this->where('id', $member_id)->firstOrFail();
        return $member;
    }
    public function setMemberInfo($member_id, $member_info)
    {
        if (json_encode($this->where('id', $member_id)->get()) === '[]') {
            return [
                'status' => 425,
                'message' => 'Error editing.',
            ];
        }
        $member = $this->where('id', $member_id)->firstOrFail();
        $member->name = $member_info['name'];
        $member->level_id = $member_info['level_id'];
        $member->name = $member_info['name'];
        $member->password = Hash::make($member_info['password']);
        if ($member->parent !== $member_info['parent']) {
            $children = $this->where('id_path', 'like', "%" . $member->id_path . "%")->orderby('level_id', 'asc')->get();
            foreach ($children as $child) {
                $parentOfChild = $this->getParent($child->id);
                $child->id_path = $parentOfChild->id_path . "#" . $parentOfChild->id;
                $child->save();
            }
            $parent = $this->getParent($member->id);
            $member->id_path = $parent->id_path . "#" . $parent->id;
            $member->save();
        }
        return [
            'status' => 201,
            'message' => 'Resource edited.',
        ];
    }
    public function addMember($member_info, $user)
    {
        if ($member_info['level_id'] == 1) {
            $id_path = "";
            $parent = "";
        } else {
            $parent = $this->getMember($member_info['parent'])[0];
            $parent = get_object_vars($parent);
            $parent_id_path = $parent["id_path"];
            $id_path = $parent_id_path . "#" . $member_info['parent'];
            $parent = $member_info['parent'];
        }
        return $this->create([
            'name' => $member_info['name'],
            'password' => Hash::make($member_info['password']),
            'level_id' => $member_info['level_id'],
            'id_path' => $id_path,
            'parent' => $parent
        ]);
    }
    public function getMember($member_id)
    {
        return DB::table("members")->where("id", $member_id)->get();
    }
    public function delMember($member_id)
    {
        if (json_encode($this->where('id', $member_id)->get()) === '[]') {
            return [
                'status' => 425,
                'message' => 'Error deleting.',
            ];
        }
        $delChildren = $this->delChilds($member_id);
        $member = $this->where('id', $member_id)->delete();
        return [
            'status' => 201,
            'message' => 'Resource deleted.',
        ];
    }
    public function getChilds($member_id)
    {
        $children = DB::table("members")->where("id_path", 'like', "%" . $member_id . "%")->get();
        return $children;
    }
    public function getParent($member_id)
    {
        $currentUser = $this->where('id', $member_id)->firstOrFail();
        $parent = $this->where('id', $currentUser->parent)->firstOrFail();
        return $parent;
    }
    public function getParents($member_id)
    {
        $currentUser = $this->where('id', $member_id)->firstOrFail();
        $parentsId = explode("#", $currentUser->id_path);

        $parents = collect([]);
        foreach ($parentsId as $parentId) {
            if (json_encode($this->where('id', $parentId)->get()) !== "[]") {
                $parents->push($this->where('id', $parentId)->firstOrFail());
            }
        }
        return $parents;
    }
    public function delChilds($member_id)
    {
        $children = DB::table("members")
            ->where("id_path", 'LIKE', "%" . $member_id . "%")
            ->delete();
        return 1;
    }
    public function search($info)
    {
        $cond = collect([]);
        foreach ($info as $key => $value) {
            $cond . push([$key, $value]);
        }
        return $this->where(get_object_vars($cond))->get();
    }

    public function getMemberList($member_id)
    {
        $members = DB::table('members as m1')
            ->select('m1.id', 'm1.name', 'm1.level_id', 'm1.parent', 'levels.name as level_name', 'm2.name as parent_name')
            ->leftJoin('levels', 'm1.level_id', '=', 'levels.id')
            ->leftJoin('members as m2', 'm1.parent', '=', 'm2.id')
            ->where('m1.id_path', 'like', '%' . $member_id . '%')
            ->get();
        return [
            'status' => 201,
            'members' => $members
        ];
    }
}
