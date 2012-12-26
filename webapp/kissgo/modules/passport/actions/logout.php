<?php
//销毁SESSION中的所有内容
session_destroy();
Response::redirect(murl('passport'));