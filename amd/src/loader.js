/**
 * AMD-модуль для подгрузки частей лонгрида по AJAX.
 *
 * @module     mod_longread/loader
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["core/ajax"], function (ajax) {
  let longreadid, partcount;
  let currentPart = 1;
  let loading = false;
  let container;

  /**
   * Инициализирует загрузчик частей лонгрида.
   *
   * @param {number} lrid Идентификатор экземпляра лонгрида.
   * @param {number} pcount Общее количество частей.
   * @return {void}
   */
  function init(lrid, pcount) {
    longreadid = lrid;
    partcount = pcount;
    container = document.getElementById("longread-content");

    // Восстанавливаем прокрутку, если есть сохранённое состояние
    const savedPartId = localStorage.getItem("longread_last_part_id_" + longreadid);
    if (savedPartId) {
      scrollToPart(savedPartId);
    }

    // Подгрузка при прокрутке
    window.addEventListener("scroll", function () {
      if (!loading && currentPart < partcount && window.scrollY + window.innerHeight > document.documentElement.scrollHeight - 200) {
        loadNextPart();
      }
    });
  }

  /**
   * Прокручивает страницу до указанного элемента.
   *
   * @param {string} partId ID элемента, до которого нужно прокрутить.
   * @return {void}
   */
  function scrollToPart(partId) {
    const target = document.getElementById(partId);
    if (target) {
      target.scrollIntoView({ behavior: "smooth" });

      // Обновляем текущую часть после прокрутки
      currentPart = parseInt(partId.split("-").pop(), 10);
    }
  }

  /**
   * Загружает следующую часть по AJAX.
   *
   * @param {Function} [callback] Функция, вызываемая после загрузки части.
   * @return {void}
   */
  function loadNextPart(callback) {
    if (loading) {
      return;
    }
    loading = true;

    ajax
      .call([
        {
          methodname: "mod_longread_get_part",
          args: { longreadid: longreadid, part: currentPart + 1 },
        },
      ])[0]
      .then(function (response) {
        if (!response.error && response.content.length > 0) {
          currentPart++;
          const partId = "longread-part-" + currentPart;
          const partEl = document.createElement("div");
          partEl.className = "longread-part";
          partEl.id = partId;
          partEl.innerHTML = "<br><br>" + response.content;
          container.appendChild(partEl);

          // Событие добавления новой части
          const event = new CustomEvent("longread:partadded", { detail: { el: partEl } });
          document.dispatchEvent(event);

          // Сохраняем ID последней части
          localStorage.setItem("longread_last_part_id_" + longreadid, partId);

          // Обновляем прогресс-бар
          require(["mod_longread/progress"], function (progress) {
            progress.setPart(currentPart);
          });

          loading = false;
          if (callback) {
            callback();
          }
        } else {
          loading = false;
          if (callback) {
            callback();
          }
        }
      })
      .catch(function () {
        loading = false;
        if (callback) {
          callback();
        }
      });
  }

  return {
    init: init,
  };
});
