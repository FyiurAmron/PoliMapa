<?php
abstract class USER_AC {
  const BAN = -1;
  const GUEST = 0;
  const USER = 1;
  const MOD = 2;
  const ADMIN = 3;
  const ROOT = 4;
}

abstract class FORM_INPUT {
  const SINGLE_LINE = 1;
  const MULTI_LINE = 2;
}
