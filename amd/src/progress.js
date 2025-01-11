/**
 * AMD-модуль для отображения прогресса чтения лонгрида и автоматической прокрутки.
 *
 * @module     mod_longread/progress
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function () {
  let totalParts, currentPart;
  let progressBar;

  /**
   * Инициализирует прогресс-бар и восстанавливает прокрутку.
   *
   * @param {number} partcount Общее число частей.
   * @param {number} startPart Номер начальной части.
   * @return {void}
   */
  function init(partcount, startPart) {
    totalParts = partcount;
    currentPart = startPart || 1;
    progressBar = document.getElementById("longread-progress-bar");
    updateProgress();
  }

  /**
   * Устанавливает текущую часть и обновляет прогресс.
   *
   * @param {number} part Номер текущей части.
   * @return {void}
   */
  function setPart(part) {
    currentPart = part;
    updateProgress();
  }

  /**
   * Обновляет ширину прогресс-бара.
   *
   * @return {void}
   */
  function updateProgress() {
    if (progressBar) {
      const percent = (currentPart / totalParts) * 100;
      progressBar.style.width = percent + "%";
    }
  }

  return {
    init: init,
    setPart: setPart,
  };
});
