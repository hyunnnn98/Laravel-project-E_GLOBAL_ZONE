# e-global-zone
영진전문대학교 글로벌 존 온라인 예약 시스템

## 목차

[1. 사용자 요구 분석 (2020-07-07)](#사용자-요구-분석)

[2. 스토리 보드 구상 (2020-07-08 ~ 2020-07-00)](#스토리-보드-구상)

[3. Sequence Diagram 작성 (2020-07-29 ~ 2020-07-30)](#Sequence-Diagram-작성)

[4. DataBase 설계 (2020-07-30 ~ 2020-07-00)](#DataBase-설계)

[5. API 설계 (2020-07-30 ~ 2020-07-00)](#API-설계)

[6. Component 구성 (2020-07-30 ~ 2020-07-00)](#Component-구성)

## 사용자 요구 분석
진행 일시 : 2020-07-07

#### 전체 기능 요약

1. 글로벌 존 이용 예약 관리
2. 글로벌 존 이용 후 통계 및 증빙 자료 자동화 출력

#### 사용자 구분

- 관리자(국제 교류원)
- 한국인 학생
- 유학생

#### 기능 구분

- 글로벌 존 근무 스케줄
    - 스케줄 입력
    - 스케줄 수정
    - 스케줄 삭제
    - 스케줄 조회
- 글로벌 존 예약
    - 예약 신청
    - 예약 취소
    - 예약 조회
    - 예약 승인
- 글로벌 존 관리대장
    - 예약 세션 별 진행결과 기록
        - 진행결과 입력
        - 진행결과 수정
        - 진행결과 조회
        - 진행결과 승인
    - 전체 이용 통계 조회
        - 유학생 기준
        - 한국인 학생 기준
- 유학생 정보
    - 조회, 관리
    - 삽입
    - 삭제
    - 수정
- 시스템 환경 설정

#### 사용자별 기능 구분

- `관리자` 는 글로벌 존 근무 스케줄을 `입력`, `수정`, `삭제`, `조회`
- `유학생` 은 글로벌 존 근무 스케쥴을 `조회`
- `한국인 학생` 은 글로벌 존 근무 스케쥴을 `조회`

- `관리자` 는 글로벌 존 예약을 `신청`, `취소`, `조회`, `승인`
- `유학생` 은 글로벌 존 예약을 `승인`
- `한국인 학생` 는 글로벌 존 예약을 `신청`, `취소`, `조회`

- `관리자` 는 진행결과를 `조회`, `수정`, `승인`
- `유학생` 은 진행결과를 `조회`, `수정`
- `한국인 학생` 은 진행결과를 `조회`

- `관리자` 는 진행결과를 전체 이용 통계를 `유학생` 및 `한국인 학생` 기준으로 `조회`
- `관리자` 는 유학생 정보를 `조회`, `삽입`, `삭제`, `수정`
- `관리자` 는 시스템 환경을 `설정`

#### 기타사항

- 유학생, 관리자 페이지는 굳이 모바일 페이지는 불필요
- 한국인 학생 → 모바일 페이지 중점
- 유학생도 모바일 페이지 포함되면 좋음
- key 값은 학번으로 사용
- 유학생 1명당 10~16시간 정도 근무
    - 1회에 20분씩 2타임을 구성
    - 참석 인원 수를 제한
    - 환경설정 기능
    
    
## 스토리 보드 구상
진행 일시 : 2020-07-08 ~
 
- [예약 및 스케줄 관리](https://s3.us-west-2.amazonaws.com/secure.notion-static.com/fd2e4017-2bfb-4d1b-b916-a4877fc9cb0a/__2.pdf?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAT73L2G45O3KS52Y5%2F20200729%2Fus-west-2%2Fs3%2Faws4_request&X-Amz-Date=20200729T180311Z&X-Amz-Expires=86400&X-Amz-Signature=da5d2b7c100b6ba5a4311a03b6f78c3a419478d3e12c51d4590de3387b54a00d&X-Amz-SignedHeaders=host&response-content-disposition=filename%20%3D%22%25E1%2584%2580%25E1%2585%25B3%25E1%2586%25AF%25E1%2584%2585%25E1%2585%25A9%25E1%2584%2587%25E1%2585%25A5%25E1%2586%25AF%25E1%2584%258C%25E1%2585%25A9%25E1%2586%25AB%25E1%2584%258B%25E1%2585%25A8%25E1%2584%258B%25E1%2585%25A3%25E1%2586%25A8_%25E1%2584%2589%25E1%2585%25B3%25E1%2584%2590%25E1%2585%25A9%25E1%2584%2585%25E1%2585%25B5%25E1%2584%2587%25E1%2585%25A9%25E1%2584%2583%25E1%2585%25B3_2%25E1%2584%258E%25E1%2585%25A1.pdf%22)
- [유학생 및 한국인 학생 관리](https://s3.us-west-2.amazonaws.com/secure.notion-static.com/4649b4bd-8292-4b21-b3c1-c45cd06027bf/_____%282020-07-17%29.pdf?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAT73L2G45O3KS52Y5%2F20200729%2Fus-west-2%2Fs3%2Faws4_request&X-Amz-Date=20200729T180122Z&X-Amz-Expires=86400&X-Amz-Signature=92a2ad8cf65f4e80ef99fad1cd16ffa2db34648371ac0448e26a1c99ef568f4b&X-Amz-SignedHeaders=host&response-content-disposition=filename%20%3D%22%25E1%2584%258B%25E1%2585%25B2%25E1%2584%2592%25E1%2585%25A1%25E1%2586%25A8%25E1%2584%2589%25E1%2585%25A2%25E1%2586%25BC%252C%2520%25E1%2584%2592%25E1%2585%25A1%25E1%2586%25AB%25E1%2584%2580%25E1%2585%25AE%25E1%2586%25A8%25E1%2584%258B%25E1%2585%25B5%25E1%2586%25AB%2520%25E1%2584%2592%25E1%2585%25A1%25E1%2586%25A8%25E1%2584%2589%25E1%2585%25A2%25E1%2586%25BC%2520%25E1%2584%2580%25E1%2585%25AA%25E1%2586%25AB%25E1%2584%2585%25E1%2585%25B5%2520%25E1%2584%2589%25E1%2585%25B3%25E1%2584%2590%25E1%2585%25A9%25E1%2584%2585%25E1%2585%25B5%2520%25E1%2584%2587%25E1%2585%25A9%25E1%2584%2583%25E1%2585%25B3%282020-07-17%29.pdf%22)
- 환경 설정 (진행중)

## Sequence Diagram 작성
진행 일시 : 2020-07-29 ~ 30

![KakaoTalk_Photo_2020-07-30-03-06-57](https://user-images.githubusercontent.com/53788601/88836506-c2a39980-d211-11ea-93eb-1bd824d7d946.png)

## DataBase 설계
진행 일시 : 2020-07-30 ~

## API 설계
진행 일시 : 2020-07-30 ~

## Component 구성
진행 일시 : 2020-07-00 ~
