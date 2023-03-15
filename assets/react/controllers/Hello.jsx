import React, { useEffect, useState } from "react";



function getTime(){
    const today = new Date();
    const time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
    return time;
}


export default function (props) {
  const [time, setTime] = useState(()=>getTime());

  useEffect(()=>{

    const clock = setInterval(()=>{setTime(getTime())}, 1000);
    return ()=>{
        clearInterval(clock);
    }
  },[])

  return (
    <div contentEditable>
      Hello, Mister {props.fullName} <strong>{time}</strong>
    </div>
  );
}
