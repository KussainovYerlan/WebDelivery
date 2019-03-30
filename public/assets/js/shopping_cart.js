let d = document,
    buttons = d.querySelectorAll('.btn-add'),
    cartCont = d.getElementById('shopping-cart_list')

const addEvent = (elem, type, handler) => {
    if(elem.addEventListener){
        elem.addEventListener(type, handler, false);
    } else {
        elem.attachEvent('on'+type, () => { handler.call( elem ) })
    }
    return false;
}

const getCartData = () => JSON.parse(localStorage.getItem('cart'))

const setCartData = (o) => {
    localStorage.setItem('cart', JSON.stringify(o))
    return false
}

const submitOrder = () => {
    let cartData = getCartData()
    $.ajax({
        type: 'POST',
        url
    })
}

function addToCart(e){
    this.disabled = true
    let cartData = getCartData() || {},
        id = this.id,
        item = this.parentNode.parentNode,
        img = item.querySelector('.product-image').src,
        name = item.querySelector('.product-name').innerHTML,
        price = item.querySelector('.product-price').innerHTML,
        description = item.querySelector('.product-description').innerHTML

    if(cartData.hasOwnProperty(id)){
        cartData[id][0] += 1
    } else {
        cartData[id] = [1, img, name, description, price]
    }
    if(!setCartData(cartData)){
        this.disabled = false
    }
    updateTotalCount()
    return false
}

function updateTotalCount() {
    let cartData = getCartData(),
        totalCount = 0

    for(let item in cartData) {
        totalCount += parseInt(cartData[item][0])
    }

    if (totalCount > 0){
        d.getElementById('shopping-cart_open').innerHTML = 'Корзина ' + totalCount
    } else {
        d.getElementById('shopping-cart_open').innerHTML = 'Корзина';
    }
}

function openCart(e){
    let cartData = getCartData(),
        totalItems = '',
        totalSum = 0

    if(cartData !== null){
        totalItems = `<table class="table modal-body">

                        <tbody id="shopping-cart_content">`
        for(let items in cartData){
            totalItems += '<tr>'
            totalItems += '<td>' + cartData[items][0] + '</td>'
            totalItems += '<td><img width="80px" src="' + cartData[items][1] + '"></td>'
            totalItems += '<td><b>' + cartData[items][2] + '</b><br><small>' + cartData[items][3] + '</smal></td>'
            totalItems += '<td>' + cartData[items][4] + '</td>'
            totalItems += '</tr>'
            totalSum += parseInt(cartData[items][4]) * parseInt(cartData[items][0])
        }
        totalItems += `</tbody>
                    </table>`
        cartCont.innerHTML = totalItems
    } else {
        cartCont.innerHTML = 'Корзина пуста'
    }

    d.getElementById('shopping-cart_total').innerHTML = totalSum

    $('#shopping-cart').modal('toggle')

    return false
}

for(var i = 0; i < buttons.length; i++){
    addEvent(buttons[i], 'click', addToCart)
}

addEvent(d.getElementById('shopping-cart_open'), 'click', openCart);

addEvent(d.getElementById('shopping-cart_clear'), 'click', function(e){
    localStorage.removeItem('cart')
    cartCont.innerHTML = 'Корзина пуста'
    d.getElementById('shopping-cart_total').innerHTML = '0'
    updateTotalCount()
})

updateTotalCount() 