import React, {useEffect, useState} from "react";
import Modal from "components/common/modal/Modal";
import InsertSchedule from "components/common/modal/InsertSchedule";
import ScheduleTable from "components/common/ScheduleTable";
import CheckBox from 'components/common/CheckBox';
import DailySchedule from "../../../../conf/class/DailySchedule";
import conf from "conf/conf";
import useModal from "../../../../modules/hooks/useModal";

/**
 * Manager :: 스케줄 조회
 * @returns {JSX.Element}
 * @constructor
 */
export default function Schedules() {
    const data = [
        [new DailySchedule(1, "1", conf.language.ENGLISH, new Array(9)),
            new DailySchedule(1, "2", conf.language.ENGLISH, new Array(9)),
            new DailySchedule(1, "3", conf.language.ENGLISH, new Array(9)),],
        [new DailySchedule(1, "4", conf.language.CHINESE, new Array(9)),
            new DailySchedule(1, "5", conf.language.CHINESE, new Array(9)),
            new DailySchedule(1, "6", conf.language.CHINESE, new Array(9)),],
        [new DailySchedule(1, "7", conf.language.JAPANESE, new Array(9)),
            new DailySchedule(1, "8", conf.language.JAPANESE, new Array(9)),]
    ];
    // const [originData, setOriginData] = useState();
    const [insertIsOpen, setInsertIsOpen] = useState(false);
    const [scheduleList, setScheduleList] = useState();
    const {isOpen, handleClose, handleOpen} = useModal();

    const origin = JSON.parse(JSON.stringify(data));
    useEffect(() => {
        setScheduleList(data);
        // setOriginData(origin);
    }, [])

    const openSch = () => {
        setInsertIsOpen(true);
    };
    const closeSch = () => {
        setInsertIsOpen(false);
    };
    const handleAll = () => {
        setScheduleList(origin)
    }
    const handleCheck = (data, status) => {
        let array = [];
        let miniArray = [];
        for (let i = 0; i < data.length; i++) {
            miniArray = [];
            for (let j = 0; j < data[i].length; j++) {
                for (let k = 0; k < data[i][j].schedule.length; k++) {
                    try {
                        if (data[i][j].schedule[k].status !== status) {
                            data[i][j].schedule[k] = null;
                        }
                    } catch {
                    }
                }
                miniArray.push(data[i][j]);
            }
            array.push(miniArray)
        }
        setScheduleList(array);
    };

    return (
        <div className="content">
            <div className="sub_title">
                <p className="tit">{new Date().toDateString().toUpperCase()}</p>
                <div className="select_date">
                    <img src="/global/img/select_date_ico.gif" alt="날짜 선택"/>
                </div>
                <CheckBox
                    data={scheduleList}
                    handleAll={handleAll}
                    handleCheck={handleCheck}
                />
            </div>
            <div className="sch_info">
                <ul>
                    <li className="ico01">[예약현황] 미승인 / 총 신청 학생</li>
                    <li className="ico02">[예약 승인 완료]</li>
                </ul>
                <ul>
                    <li className="ico03">[결과 미입력] 출석 학생</li>
                    <li className="ico04">[결과 입력 완료]</li>
                </ul>
                <ul>
                    <li className="ico05">[관리자 미승인] 출석 학생</li>
                    <li className="ico06">[관리자 승인 완료]</li>
                </ul>
            </div>
            <div className="wrap">
                {scheduleList ? <ScheduleTable
                    scheduleList={scheduleList}
                /> : <></>}

                <div className="table_btn">
                    <div onClick={handleOpen}>
                        개별 입력
                    </div>
                    <Modal isOpen={isOpen} hadleClose={handleClose}>
                        <InsertSchedule closeSch={handleClose}/>
                    </Modal>
                    <div style={{display:"none"}}>
                        {/* 추후 추가 예정 */}
                        scv
                    </div>
                </div>
            </div>
        </div>
    );
}
