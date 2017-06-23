#!/bin/bash

S_MODULE_PATH=${S_MODULE_PATH:-`readlink -f ..`}
export MSGAVG_CWD="${S_MODULE_PATH}/MSGAVG"

if [ -f "${MSGAVG_CWD}/etc/MSGAVG.conf" ]; then
   source "${MSGAVG_CWD}/etc/MSGAVG.conf"
else
    echo "`date +%H:%M:%S`: $0(pid $$): File ${MSGAVG_CWD}/etc/MSGAVG.conf not found"
fi

function MSGAVG_agent() {
	
  if [[ "$#" > "0" ]]; then
	echo "MSGAVG_agent: Current Job: ${@}"
    #send the job string directly to php
    php ${MSGAVG_CWD}/apiRequest.php "${@}" "${S_OPHEME_DIR}"
  else
    echo "ERROR: invalid number of paramaters \"$#\": \"$@\""
  fi

}