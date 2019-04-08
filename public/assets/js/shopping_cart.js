let d = document,
    addButtons = d.querySelectorAll('.btn-add'),
    cartCont = d.getElementById('shopping-cart_list'),
    hiddenPrice = 0

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
}

const delFromCart = e => {
    let cartData = getCartData() || {},
        id = e.target.parentNode.id
    
    delete cartData[id]

    setCartData(cartData)
    updateCartContent()
    updateTotalCount()
    return false
}

const delButtonTitleChange = e => {
    let element = e.target
    if (e.type == 'mouseout') {
        element.innerHTML = hiddenPrice
    } else {
        hiddenPrice = element.innerHTML
        element.innerHTML = 'Удалить'
    }
}

const updateCartContent = () => {
    let cartData = getCartData(),
        totalItems = '',
        totalSum = 0,
        totalCount = 0
    
    if(cartData !== null){
        totalItems = `<table class="table">
                        <tbody id="shopping-cart_content">`
        for(let item in cartData){
            totalItems += `<tr id="${item}">
                            <input type="hidden" id="${item}">
                            <td><img width="80px" src="${cartData[item][1]}"></td>
                            <td><b>${cartData[item][2]}</b><br><small>${cartData[item][3]}</smal></td>
                            <td>${cartData[item][0]}</td>
                            <td>x</td>
                            <td class="btn-del">${cartData[item][4]} Р</td>
                            </tr>`
            totalCount += parseInt(cartData[item][0])
            totalSum += parseInt(cartData[item][4]) * parseInt(cartData[item][0])
        }
        totalItems += `</tbody>
                    </table>`
        cartCont.innerHTML = totalItems
    } else {
        totalCount = 0
    }

    if (totalCount == 0) {
        cartCont.innerHTML = 'Корзина пуста'
    }

    d.getElementById('shopping-cart_total').innerHTML = totalSum

    let delButtons = d.querySelectorAll('.btn-del')
    for(var i = 0; i < delButtons.length; i++){
        addEvent(delButtons[i], 'click', delFromCart)
        addEvent(delButtons[i], 'mouseover', delButtonTitleChange)
        addEvent(delButtons[i], 'mouseout', delButtonTitleChange)
        delButtons[i].style.cursor = 'pointer'
    }
}

const addToCart = e => {
    let cartData = getCartData() || {},
        id = e.target.id,
        item = e.target.parentNode.parentNode,
        img = item.querySelector('.product-image').src,
        name = item.querySelector('.product-name').innerHTML,
        price = item.querySelector('.product-price').innerHTML,
        description = item.querySelector('.product-description').innerHTML

    if(cartData.hasOwnProperty(id)){
        cartData[id][0] += 1
    } else {
        cartData[id] = [1, img, name, description, price]
    }
    setCartData(cartData)
    updateTotalCount()
}

const updateTotalCount = () => {
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

const openCart = () => {
    updateCartContent()
    $('#shopping-cart').modal('toggle')
    return false
}

const sendCart = (e) => {
    let cartData = getCartData() || {}
    let sendData = {}
    for (let id in cartData) {
        sendData[id] = cartData[id][0]
    }
    $.ajax({
        type: "POST",
        url: '/checkout/cart',
        data: {
            products: JSON.stringify(sendData),
        },
        datatype: "json"
    })
}

for(var i = 0; i < addButtons.length; i++){
    addEvent(addButtons[i], 'click', addToCart)
}

addEvent(d.getElementById('shopping-cart_open'), 'click', openCart);
addEvent(d.getElementById('shopping-cart_send'), 'click', sendCart);

addEvent(d.getElementById('shopping-cart_clear'), 'click', e => {
    localStorage.removeItem('cart')
    d.getElementById('shopping-cart_list').innerHTML = 'Корзина пуста'
    d.getElementById('shopping-cart_total').innerHTML = '0'
    updateTotalCount()
})

updateTotalCount() 

