export default {
    timer: null,

    debounce: function (f, ms) {
        let debThis = this;

        return function (...args) {
            const onComplete = () => {
                f.apply(this, args);
                debThis.timer = null;
            };

            if (debThis.timer) {
                clearTimeout(debThis.timer);
            }

            debThis.timer = setTimeout(onComplete, ms);
        };
    }
}