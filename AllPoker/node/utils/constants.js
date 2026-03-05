// utils/constants.js
const CASH_OPERATION = Object.freeze({
  ADD: "add_cash", // When money is added to user balance
  CUT: "cut_cash", // When money is deducted
});

const CASH_OPERATION_LABELS = Object.freeze({
  [CASH_OPERATION.ADD]: "Gain",
  [CASH_OPERATION.CUT]: "Loss",
});

module.exports = {
  CASH_OPERATION,
  CASH_OPERATION_LABELS,
};
