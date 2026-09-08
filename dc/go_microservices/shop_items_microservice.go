package main

import (
 "fmt"
 "database/sql" 
 "github.com/gin-gonic/gin"
 _ "github.com/go-sql-driver/mysql"
 "github.com/jinzhu/gorm"
 _ "github.com/jinzhu/gorm/dialects/mysql"
 "time"
)

var db *gorm.DB
var err error

type ShopItems struct {
	Id int64 `db:"id" json:"id" form:"id"`
	Company sql.NullString `db:"company" json:"company" form:"company"`
	ShopCode sql.NullString `db:"shop_code" json:"shop_code" form:"shop_code"`
	LanguageCode sql.NullString `db:"language_code" json:"language_code" form:"language_code"`
	ItemNo sql.NullString `db:"item_no" json:"item_no" form:"item_no"`
	Description sql.NullString `db:"description" json:"description" form:"description"`
	Summary sql.NullString `db:"summary" json:"summary" form:"summary"`
	BaseUnitOfMeasure sql.NullString `db:"base_unit_of_measure" json:"base_unit_of_measure" form:"base_unit_of_measure"`
	UnitOfMeasureCode sql.NullString `db:"unit_of_measure_code" json:"unit_of_measure_code" form:"unit_of_measure_code"`
	NavBaseUnitCode sql.NullString `db:"nav_base_unit_code" json:"nav_base_unit_code" form:"nav_base_unit_code"`
	Multiplier sql.NullFloat64 `db:"multiplier" json:"multiplier" form:"multiplier"`
	VariantType sql.NullString `db:"variant_type" json:"variant_type" form:"variant_type"`
	Active sql.NullBool `db:"active" json:"active" form:"active"`
	ValidityFrom NullTime `db:"validity_from" json:"validity_from" form:"validity_from"`
	ValidityTo NullTime `db:"validity_to" json:"validity_to" form:"validity_to"`
	MainPictureLineNo sql.NullInt64 `db:"main_picture_line_no" json:"main_picture_line_no" form:"main_picture_line_no"`
	MainCategoryLineNo sql.NullInt64 `db:"main_category_line_no" json:"main_category_line_no" form:"main_category_line_no"`
	RetailPrice sql.NullFloat64 `db:"retail_price" json:"retail_price" form:"retail_price"`
	BasePrice sql.NullFloat64 `db:"base_price" json:"base_price" form:"base_price"`
	PriceIncludesVat sql.NullBool `db:"price_includes_vat" json:"price_includes_vat" form:"price_includes_vat"`
	Inventory sql.NullFloat64 `db:"inventory" json:"inventory" form:"inventory"`
	InsufficientInventoryLimit sql.NullFloat64 `db:"insufficient_inventory_limit" json:"insufficient_inventory_limit" form:"insufficient_inventory_limit"`
	QuantityOnPurchaseOrder sql.NullFloat64 `db:"quantity_on_purchase_order" json:"quantity_on_purchase_order" form:"quantity_on_purchase_order"`
	DiscountGroup sql.NullString `db:"discount_group" json:"discount_group" form:"discount_group"`
	AllowInvoiceDiscount sql.NullBool `db:"allow_invoice_discount" json:"allow_invoice_discount" form:"allow_invoice_discount"`
	SearchQuery sql.NullString `db:"search_query" json:"search_query" form:"search_query"`
	VendorNo sql.NullString `db:"vendor_no" json:"vendor_no" form:"vendor_no"`
	VendorName sql.NullString `db:"vendor_name" json:"vendor_name" form:"vendor_name"`
	ParentItemNo sql.NullString `db:"parent_item_no" json:"parent_item_no" form:"parent_item_no"`
	OrderRanking sql.NullFloat64 `db:"order_ranking" json:"order_ranking" form:"order_ranking"`
	Weight sql.NullFloat64 `db:"weight" json:"weight" form:"weight"`
	NetWeight sql.NullFloat64 `db:"net_weight" json:"net_weight" form:"net_weight"`
	Width sql.NullFloat64 `db:"width" json:"width" form:"width"`
	Height sql.NullFloat64 `db:"height" json:"height" form:"height"`
	Length sql.NullFloat64 `db:"length" json:"length" form:"length"`
	Volume sql.NullFloat64 `db:"volume" json:"volume" form:"volume"`
	CreationDate NullTime `db:"creation_date" json:"creation_date" form:"creation_date"`
	MetaKeywords sql.NullString `db:"meta_keywords" json:"meta_keywords" form:"meta_keywords"`
	MetaDescription sql.NullString `db:"meta_description" json:"meta_description" form:"meta_description"`
	SiteTitle sql.NullString `db:"site_title" json:"site_title" form:"site_title"`
	AllowGiftPackage sql.NullBool `db:"allow_gift_package" json:"allow_gift_package" form:"allow_gift_package"`
	IsGiftPackage sql.NullBool `db:"is_gift_package" json:"is_gift_package" form:"is_gift_package"`
	MinimumOrderQuantity sql.NullFloat64 `db:"minimum_order_quantity" json:"minimum_order_quantity" form:"minimum_order_quantity"`
	QuantityPackingUnit sql.NullFloat64 `db:"quantity_packing_unit" json:"quantity_packing_unit" form:"quantity_packing_unit"`
	OrderPerPackingUnit sql.NullBool `db:"order_per_packing_unit" json:"order_per_packing_unit" form:"order_per_packing_unit"`
	VatProdPostingGroup sql.NullString `db:"vat_prod_posting_group" json:"vat_prod_posting_group" form:"vat_prod_posting_group"`
	Customizable sql.NullBool `db:"customizable" json:"customizable" form:"customizable"`
	CustomizationPrice sql.NullFloat64 `db:"customization_price" json:"customization_price" form:"customization_price"`
	CanonicalUrl sql.NullString `db:"canonical_url" json:"canonical_url" form:"canonical_url"`
	ToDelete sql.NullBool `db:"to_delete" json:"to_delete" form:"to_delete"`
	MainPreviewImageFilename sql.NullString `db:"main_preview_image_filename" json:"main_preview_image_filename" form:"main_preview_image_filename"`
	ShippingCategory sql.NullInt64 `db:"shipping_category" json:"shipping_category" form:"shipping_category"`
	AlwaysAvailable sql.NullBool `db:"always_available" json:"always_available" form:"always_available"`
	CreatedAt NullTime `db:"created_at" json:"created_at" form:"created_at"`
	UpdatedAt NullTime `db:"updated_at" json:"updated_at" form:"updated_at"`
	DeletedAt NullTime `db:"deleted_at" json:"deleted_at" form:"deleted_at"`
	Id int64 `db:"id" json:"id" form:"id"`
	Company string `db:"company" json:"company" form:"company"`
	ShopCode string `db:"shop_code" json:"shop_code" form:"shop_code"`
	LanguageCode string `db:"language_code" json:"language_code" form:"language_code"`
	ItemNo string `db:"item_no" json:"item_no" form:"item_no"`
	Description string `db:"description" json:"description" form:"description"`
	Summary string `db:"summary" json:"summary" form:"summary"`
	BaseUnitOfMeasure string `db:"base_unit_of_measure" json:"base_unit_of_measure" form:"base_unit_of_measure"`
	UnitOfMeasureCode string `db:"unit_of_measure_code" json:"unit_of_measure_code" form:"unit_of_measure_code"`
	NavBaseUnitCode string `db:"nav_base_unit_code" json:"nav_base_unit_code" form:"nav_base_unit_code"`
	Multiplier float64 `db:"multiplier" json:"multiplier" form:"multiplier"`
	VariantType string `db:"variant_type" json:"variant_type" form:"variant_type"`
	Active bool `db:"active" json:"active" form:"active"`
	ValidityFrom time.Time `db:"validity_from" json:"validity_from" form:"validity_from"`
	ValidityTo time.Time `db:"validity_to" json:"validity_to" form:"validity_to"`
	MainPictureLineNo int64 `db:"main_picture_line_no" json:"main_picture_line_no" form:"main_picture_line_no"`
	MainCategoryLineNo int64 `db:"main_category_line_no" json:"main_category_line_no" form:"main_category_line_no"`
	RetailPrice float64 `db:"retail_price" json:"retail_price" form:"retail_price"`
	BasePrice float64 `db:"base_price" json:"base_price" form:"base_price"`
	PriceIncludesVat bool `db:"price_includes_vat" json:"price_includes_vat" form:"price_includes_vat"`
	Inventory float64 `db:"inventory" json:"inventory" form:"inventory"`
	InsufficientInventoryLimit float64 `db:"insufficient_inventory_limit" json:"insufficient_inventory_limit" form:"insufficient_inventory_limit"`
	QuantityOnPurchaseOrder float64 `db:"quantity_on_purchase_order" json:"quantity_on_purchase_order" form:"quantity_on_purchase_order"`
	DiscountGroup string `db:"discount_group" json:"discount_group" form:"discount_group"`
	AllowInvoiceDiscount bool `db:"allow_invoice_discount" json:"allow_invoice_discount" form:"allow_invoice_discount"`
	SearchQuery string `db:"search_query" json:"search_query" form:"search_query"`
	VendorNo string `db:"vendor_no" json:"vendor_no" form:"vendor_no"`
	VendorName string `db:"vendor_name" json:"vendor_name" form:"vendor_name"`
	ParentItemNo string `db:"parent_item_no" json:"parent_item_no" form:"parent_item_no"`
	OrderRanking float64 `db:"order_ranking" json:"order_ranking" form:"order_ranking"`
	Weight float64 `db:"weight" json:"weight" form:"weight"`
	NetWeight float64 `db:"net_weight" json:"net_weight" form:"net_weight"`
	Width float64 `db:"width" json:"width" form:"width"`
	Height float64 `db:"height" json:"height" form:"height"`
	Length float64 `db:"length" json:"length" form:"length"`
	Volume float64 `db:"volume" json:"volume" form:"volume"`
	CreationDate time.Time `db:"creation_date" json:"creation_date" form:"creation_date"`
	MetaKeywords string `db:"meta_keywords" json:"meta_keywords" form:"meta_keywords"`
	MetaDescription string `db:"meta_description" json:"meta_description" form:"meta_description"`
	SiteTitle string `db:"site_title" json:"site_title" form:"site_title"`
	AllowGiftPackage bool `db:"allow_gift_package" json:"allow_gift_package" form:"allow_gift_package"`
	IsGiftPackage bool `db:"is_gift_package" json:"is_gift_package" form:"is_gift_package"`
	MinimumOrderQuantity float64 `db:"minimum_order_quantity" json:"minimum_order_quantity" form:"minimum_order_quantity"`
	QuantityPackingUnit float64 `db:"quantity_packing_unit" json:"quantity_packing_unit" form:"quantity_packing_unit"`
	OrderPerPackingUnit bool `db:"order_per_packing_unit" json:"order_per_packing_unit" form:"order_per_packing_unit"`
	VatProdPostingGroup string `db:"vat_prod_posting_group" json:"vat_prod_posting_group" form:"vat_prod_posting_group"`
	Customizable bool `db:"customizable" json:"customizable" form:"customizable"`
	CustomizationPrice float64 `db:"customization_price" json:"customization_price" form:"customization_price"`
	CanonicalUrl string `db:"canonical_url" json:"canonical_url" form:"canonical_url"`
	ToDelete bool `db:"to_delete" json:"to_delete" form:"to_delete"`
	MainPreviewImageFilename sql.NullString `db:"main_preview_image_filename" json:"main_preview_image_filename" form:"main_preview_image_filename"`
	ShippingCategory int64 `db:"shipping_category" json:"shipping_category" form:"shipping_category"`
	AlwaysAvailable bool `db:"always_available" json:"always_available" form:"always_available"`

}

func main() {
    db, _ = gorm.Open("mysql", "root:123@tct(127.0.0.1:3306)/gotest?charset=utf8&parseTime=True&loc=Local")
    
    if err != nil {
        fmt.Println(err)
    }
    defer db.Close()
    
    db.AutoMigrate(&ShopItems{})
    
    r := gin.Default()
    r.GET("/shop_items/", GetShopItemss)
    r.GET("/shop_items/:id", GetShopItems)
    r.POST("/shop_items", CreateShopItems)
    r.PUT("/shop_items/:id", UpdateShopItems)
    r.DELETE("/shop_items/:id", DeleteShopItems)
    
    r.Run(":8080")
}

func GetShopItemss(c *gin.Context) {
    var models []ShopItems
    if err := db.Find(&models).Error; err != nil {
        c.AbortWithStatus(404)
        fmt.Println(err)
    } else {
        c.JSON(200, models)
    }

}

func GetShopItems(c *gin.Context) {
    id := c.Params.ByName("id")
    var model ShopItems
    if err := db.Where("id = ?", id).First(&model).Error; err != nil {
        c.AbortWithStatus(404)
        fmt.Println(err)
    } else {
        c.JSON(200, model)
    }
}

func CreateShopItems(c *gin.Context) {
    var model ShopItems
    c.BindJSON(&model)

    db.Create(&model)
    c.JSON(200, model)
}

func UpdateShopItems(c *gin.Context) {
    var model ShopItems
    id := c.Params.ByName("id")

    if err := db.Where("id = ?", id).First(&model).Error; err != nil {
        c.AbortWithStatus(404)
        fmt.Println(err)
    }
    c.BindJSON(&model)

    db.Save(&model)
    c.JSON(200, model)
}

func DeleteShopItems(c *gin.Context) {
    id := c.Params.ByName("id")
    var model ShopItems
    d := db.Where("id = ?", id).Delete(&model)
    fmt.Println(d)
    c.JSON(200, gin.H{"id #" + id: "deleted"})
}

