export const phoneMask = (el, binding) => {
    var mask = binding.value,
        first = mask.indexOf('_'),
        fieldsL = mask.replace(/[^_]/gm, '').length,
        clean = mask.replace(/[^0-9_]/gm, ''),
        indexes = []


    for (var i = 0; i < clean.length; i++) {
        if (!isNaN(clean[i])) {
            indexes.push(i)
        }
    }

    el.value = mask
    el.clean = mask.replace(/[^0-9]/gm, '')

    function maskIt(event, start) {
        var value = el.value,
            filtred = value.replace(/[^0-9]/gm, ''),
            result = ''

        if (value.length < first) {
            value = mask + value
            filtred = value.replace(/[^0-9]/gm, '')
        }

        for (var i = 0; i < filtred.length; i++) {
            if (indexes.indexOf(i) === -1) {
                result += filtred[i]
            }
        }

        value = ''
        var cursor = 0

        for (var i = 0; i < mask.length; i++) {
            if (mask[i] === '_' && result) {
                value += result[0]
                result = result.slice(1)
                cursor = i + 1

            } else {
                value += mask[i]
            }
        }

        if (cursor < first) {
            cursor = first
        }

        el.value = value

        el.clean = el.value.replace(/[^0-9]/gm, '')

        el.setSelectionRange(cursor, cursor)
    }

    el.addEventListener('focus', function (event) {
        event.preventDefault()
    })

    el.addEventListener('click', function (event) {
        event.preventDefault()
        var start = el.value.indexOf('_')

        if (start === -1) {
            start = el.value.length
        }

        el.setSelectionRange(start, start)

    })

    el.addEventListener('paste', function (event) {
        var start = el.selectionStart

        if (start < first) {
            el.value = '_' + el.value
        }
    })

    el.addEventListener('input', function (event) {
        var start = el.selectionStart
        maskIt(event, start)
    })

}
