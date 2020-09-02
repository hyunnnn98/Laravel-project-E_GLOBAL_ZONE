<?php

namespace App\Http\Controllers;

use App\Schedule;
use App\Student_foreigner;
use App\Student_foreigners_contact;
use App\Work_student_foreigner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class ForeignerController extends Controller
{
    /*
     * [Refactoring]
     * TODO RESPONSE 수정
     * TODO 접근 가능 범위 수정
     */
    private const _WORK_STD_FOR_INDEX_SUCCESS = "의 근로유학생 목록 조회에 성공하였습니다.";
    private const _WORK_STD_FOR_INDEX_FAILURE = "등록된 근로유학생이 없습니다.";

    private const _STD_FOR_SHOW_SUCCESS = "유학생 정보조회에 성공하였습니다.";
    private const _STD_FOR_SHOW_FAILURE = "유학생 정보조회에 실패하였습니다.";

    // 0000학기의 근로유학생 목록에 00명이 동록되었습니다.
    private const _SECT_STD_FOR_STORE_SUCCESS1 = "의 근로유학생 목록에 ";
    private const _SECT_STD_FOR_STORE_SUCCESS2 = "명이 등록되었습니다.";
    // 0000학기 근로유학생 등록에 실패하였습니다.
    private const _SECT_STD_FOR_STORE_FAILURE = " 근로유학생 등록에 실패하였습니다.";

    // 000 유학생이 0000학기의 근로유학생 목록에 등록되었습니다.
    // 000 유학생의 근로유학생 등록에 실패하였습니다.
    private const _SECT_STD_FOR_EACH = " 유학생이 ";

    private const _SECT_STD_FOR_EACH_STORE_SUCCESS = " 근로유학생 목록에 등록되었습니다.";
    private const _SECT_STD_FOR_EACH_STORE_FAILURE = " 유학생의 근로유학생 등록에 실패하였습니다.";

    // 000 유학생이 0000학기의 근로유학생 목록에서 삭제되었습니다.
    // 000 유학생의 근로유학생 삭제에 실패하였습니다.
    private const _SECT_STD_FOR_EACH_DELETE_SUCCESS = " 근로유학생 목록에서 삭제되었습니다.";
    private const _SECT_STD_FOR_EACH_DELETE_FAILURE = " 유학생의 근로유학생 삭제에 실패하였습니다.";

    // 000 유학생이 등록되었습니다.
    // 000 유학생 등록에 실패하였습니다.
    private const _STD_FOR_STORE_SUCCESS = " 유학생이 등록되었습니다.";
    private const _STD_FOR_STORE_FAILURE = " 유학생 등록에 실패하였습니다.";

    private const _STD_FOR_INIT_PASSWORD = "1q2w3e4r!";
    // 000 유학생의 비밀번호가 초기화가 성공하였습니다. (초기 비밀번호 : 1q2w3e4r!)
    // 000 유학생의 비밀번호가 초기화에 실패하였습니다.
    private const _STD_FOR_RESET_SUCCESS = " 비밀번호 초기화가 성공하였습니다.";
    private const _STD_FOR_RESET_FAILURE = " 비밀번호 변경에 실패하였습니다.";

    private const _STD_FOR_FAVORITE_SUCCESS = "유학생 즐겨찾기 변경에 성공하였습니다.";
    private const _STD_FOR_FAVORITE_FAILURE = "유학생 즐겨찾기 변경에 실패하였습니다.";

    // 000 유학생이 삭제되었습니다.
    // 000 유학생 삭제에 실패하였습니다.
    private const _STD_FOR_DELETE_SUCCESS = " 유학생이 삭제되었습니다.";
    private const _STD_FOR_DELETE_FAILURE = " 유학생 삭제에 실패하였습니다.";

    // 000 학번의 학생의 데이터가 중복입니다.
    private const _STD_FOR_DUPLICATED_DATA = " 학번의 학생의 데이터가 중복입니다.";


    private $schedule;

    public function __construct()
    {
        $this->schedule = new Schedule();
    }

    /**
     * 특정 유학생 정보 조회
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $rules = [
            'foreigners' => 'required|array',
            'foreigners.*' => 'required|integer|distinct|min:1000000|max:9999999',
            'guard' => 'required|string|in:admin'
        ];

        $validated_result = self::request_validator(
            $request,
            $rules,
            self::_STD_FOR_SHOW_FAILURE
        );

        if (is_object($validated_result)) {
            return $validated_result;
        }

        $req_std_for_id = $request->input('foreigners');

        $select_column = [
            'student_foreigners.std_for_id',
            'student_foreigners.std_for_name',
            'contact.std_for_phone',
            'contact.std_for_mail',
            'contact.std_for_zoom_id'
        ];

        $data_std_for = [];

        // 학생 정보 저장
        foreach ($req_std_for_id as $std_for_id) {
            // 학번 기준 검색
            $search_result =
                Student_foreigner::select($select_column)
                    ->join('student_foreigners_contacts as contact', 'student_foreigners.std_for_id', 'contact.std_for_id')
                    ->where('student_foreigners.std_for_id', $std_for_id)->get()->first();

            // 검색 결과 저장
            if ($search_result) {
                $data_std_for[] = $search_result;
            }
        }

        return response()->json([
            'message' => self::_STD_FOR_SHOW_SUCCESS,
            'data' => $data_std_for,
        ], 200);
    }

    /**
     * 학기별 유학생 등록
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $rules = [
            'foreigners' => 'required|array',
            'foreigners.*' => 'required|integer|distinct|min:1000000|max:9999999',
            'sect_id' => 'required|integer|distinct|min:0|max:100',
            'guard' => 'required|string|in:admin'
        ];

        $validated_result = self::request_validator(
            $request,
            $rules,
            self::_STD_FOR_STORE_FAILURE
        );

        if (is_object($validated_result)) {
            return $validated_result;
        }

        $req_std_for_id = $request->input('foreigners');
        $req_sect_id = $request->input('sect_id');

        // 학생 정보 저장
        foreach ($req_std_for_id as $foreigner_id) {
            // 존재하는 유학생인지 검사
            if (!Student_foreigner::find($foreigner_id)) continue;

            // 이미 해당 학기에 등록한 학생인 경우
            $isDuplicatedStudent = Work_student_foreigner::where('work_std_for', $foreigner_id)
                ->where('work_sect', $req_sect_id)
                ->count();

            if ($isDuplicatedStudent) {
                return self::response_json(self::_STD_FOR_DUPLICATED_DATA, 422);
            }

            Work_student_foreigner::create([
                'work_std_for' => $foreigner_id,
                'work_sect' => $req_sect_id
            ]);
        }

        return self::response_json(self::_SECT_STD_FOR_EACH_STORE_SUCCESS, 201);
    }

    /**
     * 학기별 유학생 수정 (=삭제)
     *
     * @param Work_student_foreigner $work_list_id
     * @return JsonResponse
     */
    public function destroy(Request $request, Work_student_foreigner $work_list_id): JsonResponse
    {
        // <<-- Request 요청 관리자 권한 검사.
        $is_admin = self::is_admin($request);

        if (is_object($is_admin)) {
            return $is_admin;
        }
        // -->>

        $work_list_id->delete();

        return self::response_json(self::_SECT_STD_FOR_EACH_DELETE_SUCCESS, 200);
    }

    /**
     * 유학생 계정 생성
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function registerAccount(Request $request): JsonResponse
    {
        // $this->validate($request, [
        //     'url' => 'unique:site1,your_column_name|unique:site2:your_column_name_2'
        // ]);

        $rules = [
            'std_for_id' => 'required|integer|unique:student_foreigners,std_for_id|unique:student_koreans,std_kor_id|distinct|min:1000000|max:9999999',
            'std_for_dept' => 'required|integer',
            'std_for_name' => 'required|string|min:2',
            'std_for_lang' => 'required|string|min:2',
            'std_for_country' => 'required|string|min:2',
            'std_for_phone' => 'required|string|unique:student_foreigners_contacts,std_for_phone',                  /* (주의) 유학생중 휴대폰이 없는 학생도 많다 */
            'std_for_mail' => 'required|email|unique:student_foreigners_contacts,std_for_mail',
            'std_for_zoom_id' => 'required|string|unique:student_foreigners_contacts,std_for_zoom_id',
            'guard' => 'required|string|in:admin'
        ];

        $validated_result = self::request_validator(
            $request,
            $rules,
            self::_STD_FOR_STORE_FAILURE
        );

        if (is_object($validated_result)) {
            return $validated_result;
        }

        // 계정 생성
        Student_foreigner::create([
            'std_for_id' => $request->input('std_for_id'),
            'password' => Hash::make(self::_STD_FOR_INIT_PASSWORD),
            'std_for_dept' => $request->input('std_for_dept'),
            'std_for_name' => $request->input('std_for_name'),
            'std_for_lang' => $request->input('std_for_lang'),
            'std_for_country' => $request->input('std_for_country'),
        ]);

        // 연락처 정보 등록
        Student_foreigners_contact::create([
            'std_for_id' => $request->input('std_for_id'),
            'std_for_phone' => $request->input('std_for_phone'),
            'std_for_mail' => $request->input('std_for_mail'),
            'std_for_zoom_id' => $request->input('std_for_zoom_id'),
        ]);

        return self::response_json(self::_STD_FOR_STORE_SUCCESS, 201);
    }

    /**
     * 유학생 비밀번호 초기화
     *
     * @param Student_foreigner $std_for_id
     * @param Request $request
     * @return Response
     */
    public function updateAccount(?Student_foreigner $std_for_id, Request $request): JsonResponse
    {
        $rules = [
            'guard' => 'required|string|in:foreigner,admin',
            'password' => 'nullable|string|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
        ];

        $validated_result = self::request_validator(
            $request,
            $rules,
            self::_STD_FOR_RESET_FAILURE
        );

        if (is_object($validated_result)) {
            return $validated_result;
        }

        if ($request->input('guard') == 'foreigner') {
            $std_for_id = $request->user($request->input('guard'))['std_for_id'];
            Student_foreigner::find($std_for_id)->update([
                'password' => Hash::make($request->input('password')),
            ]);
        } else {
            $std_for_id->update([
                'password' => Hash::make(self::_STD_FOR_INIT_PASSWORD),
            ]);
        }


        return self::response_json(self::_STD_FOR_RESET_SUCCESS, 200);
    }

    /**
     * 유학생 계정 삭제
     *
     * @param int $std_for_id
     * @return Response
     */
    public function destroyAccount(
        Request $request,
        Student_foreigner $std_for_id
    ): JsonResponse {
        // <<-- Request 요청 관리자 권한 검사.
        $is_admin = self::is_admin($request);

        if (is_object($is_admin)) {
            return $is_admin;
        }
        // -->>

        // 1. 연락처 정보 삭제
        Student_foreigners_contact::find($std_for_id['std_for_id'])->delete();
        // 2. 계정 삭제
        $std_for_id->delete();

        return self::response_json(self::_STD_FOR_DELETE_SUCCESS, 200);
    }

    /**
     * 유학생 즐겨찾기 등록 / 해제
     *
     * @param int $std_for_id
     * @param Request $request
     * @return Response
     */
    public function set_std_for_favorite(Student_foreigner $std_for_id, Request $request): JsonResponse
    {
        $rules = [
            'favorite_bool' => 'required|bool',
            'guard' => 'required|string|in:admin'
        ];

        $validated_result = self::request_validator(
            $request,
            $rules,
            self::_STD_FOR_FAVORITE_FAILURE
        );

        if (is_object($validated_result)) {
            return $validated_result;
        }

        $std_for_id->update(['std_for_state_of_favorite' => (int)$request->input('favorite_bool')]);

        return self::response_json(self::_STD_FOR_FAVORITE_SUCCESS, 200);
    }
}